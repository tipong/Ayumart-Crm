<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KurirController extends Controller
{
    public function index()
    {
        try {
            // Get current kurir user
            $user = Auth::user();

            // Verify user has kurir role (id_role = 4)
            // Note: System doesn't have roles table, using id_role directly
            if ($user->id_role != 4) {
                return redirect()->route('home')->with('error', 'Akses ditolak. Halaman ini hanya untuk kurir.');
            }

            // Get staff record for this kurir (must have staff record)
            $staff = DB::table('tb_staff')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$staff) {
                return redirect()->back()->with('error', 'Data staff kurir tidak ditemukan. Hubungi administrator.');
            }

            $staffId = $staff->id_staff;

            // Query builder untuk pengiriman
            // HANYA tampilkan pengiriman yang sudah bayar
            $baseQuery = DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
                ->leftJoin('users as pelanggan_user', 'tb_pelanggan.id_user', '=', 'pelanggan_user.id_user')
                ->leftJoin('tb_staff', 'tb_pengiriman.id_staff', '=', 'tb_staff.id_staff')
                ->leftJoin('users as staff_user', 'tb_staff.id_user', '=', 'staff_user.id_user')
                ->leftJoin('customer_addresses', 'tb_pengiriman.id_address', '=', 'customer_addresses.id')
                ->where('tb_transaksi.status_pembayaran', 'sudah_bayar') // CRITICAL: Hanya tampilkan yang sudah bayar
                ->select(
                    'tb_pengiriman.id_pengiriman',
                    'tb_pengiriman.id_address',
                    'tb_pengiriman.id_transaksi',
                    'tb_pengiriman.id_staff',
                    'tb_pengiriman.no_resi',
                    'tb_pengiriman.nama_penerima',
                    'tb_pengiriman.alamat_penerima',
                    'tb_pengiriman.status_pengiriman',
                    'tb_pengiriman.tgl_kirim',
                    'tb_pengiriman.tgl_sampai',
                    'tb_transaksi.kode_transaksi as kode_transaksi_full',
                    'tb_transaksi.total_harga',
                    'tb_transaksi.status_pembayaran',
                    'tb_transaksi.id_pelanggan',
                    'tb_pelanggan.nama_pelanggan',
                    'pelanggan_user.email as email_pelanggan',
                    'tb_staff.nama_staff',
                    'staff_user.id_user as id_kurir',
                    'customer_addresses.alamat_lengkap',
                    'customer_addresses.kota',
                    'customer_addresses.kecamatan',
                    'customer_addresses.kode_pos',
                    'customer_addresses.latitude',
                    'customer_addresses.longitude'
                );

            // Show deliveries:
            // 1. Unassigned deliveries (id_staff = NULL) AND status ready for pickup
            // 2. Deliveries assigned to this kurir (id_staff's user_id = current user id)
            // NOTE: We don't filter here, we filter in each specific query below

            // Get delivery statistics
            // Pending = ready to be picked up (dikemas)
            // Can be unassigned OR assigned to this kurir
            $pendingDeliveries = (clone $baseQuery)
                ->where('tb_pengiriman.status_pengiriman', 'dikemas')
                ->where(function($query) use ($staffId) {
                    // Show unassigned OR assigned to this kurir
                    $query->whereNull('tb_pengiriman.id_staff')
                          ->orWhere('tb_pengiriman.id_staff', $staffId);
                })
                ->count();

            // On delivery = status_pengiriman is 'dalam_pengiriman'
            $ongoingDeliveries = (clone $baseQuery)
                ->where('tb_pengiriman.status_pengiriman', 'dalam_pengiriman')
                ->where('tb_pengiriman.id_staff', $staffId)
                ->count();

            // Completed today = status_pengiriman is 'selesai' and tgl_sampai is today
            $completedToday = (clone $baseQuery)
                ->where('tb_pengiriman.status_pengiriman', 'selesai')
                ->where('tb_pengiriman.id_staff', $staffId)
                ->whereDate('tb_pengiriman.tgl_sampai', today())
                ->count();

            // Total deliveries for this kurir (all time)
            $totalDeliveries = DB::table('tb_pengiriman')
                ->where('tb_pengiriman.id_staff', $staffId)
                ->where('tb_pengiriman.status_pengiriman', 'selesai')
                ->count();

            // Get pending orders (waiting to be picked up)
            // Include unassigned AND assigned to this kurir
            $pendingOrders = DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
                ->leftJoin('tb_cabang', 'tb_transaksi.id_cabang', '=', 'tb_cabang.id_cabang')
                ->leftJoin('customer_addresses', 'tb_pengiriman.id_address', '=', 'customer_addresses.id')
                ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                ->where('tb_pengiriman.status_pengiriman', 'dikemas')
                ->where(function($query) use ($staffId) {
                    // Show unassigned OR assigned to this kurir
                    $query->whereNull('tb_pengiriman.id_staff')
                          ->orWhere('tb_pengiriman.id_staff', $staffId);
                })
                ->select(
                    'tb_pengiriman.id_pengiriman',
                    'tb_pengiriman.id_staff',
                    'tb_pengiriman.id_address',
                    'tb_pengiriman.id_transaksi',
                    'tb_pengiriman.no_resi',
                    'tb_pengiriman.nama_penerima',
                    'tb_pengiriman.alamat_penerima',
                    'tb_pengiriman.status_pengiriman',
                    'tb_pengiriman.tgl_kirim',
                    'tb_transaksi.kode_transaksi as kode_transaksi_full',
                    'tb_transaksi.id_pelanggan',
                    'tb_transaksi.tanggal_transaksi',
                    'tb_pelanggan.nama_pelanggan',
                    DB::raw('tb_pengiriman.id_staff as id_kurir'),
                    'tb_cabang.nama_cabang',
                    'tb_cabang.alamat as alamat_cabang',
                    'tb_cabang.no_telepon as telp_cabang',
                    'customer_addresses.alamat_lengkap',
                    'customer_addresses.kota',
                    'customer_addresses.kecamatan',
                    'customer_addresses.kode_pos',
                    'customer_addresses.latitude',
                    'customer_addresses.longitude',
                    'customer_addresses.nama_penerima as nama_penerima_address',
                    'customer_addresses.no_telp_penerima'
                )
                ->orderBy('tb_pengiriman.id_pengiriman', 'desc')
                ->get();

            Log::info("Pending Orders Query Result", [
                'count' => $pendingOrders->count(),
                'user_id' => $user->id_user,
                'data' => $pendingOrders->toArray()
            ]);

            // Get ongoing orders (currently being delivered by this kurir)
            $ongoingOrders = DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
                ->leftJoin('tb_cabang', 'tb_transaksi.id_cabang', '=', 'tb_cabang.id_cabang')
                ->leftJoin('customer_addresses', 'tb_pengiriman.id_address', '=', 'customer_addresses.id')
                ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                ->where('tb_pengiriman.status_pengiriman', 'dalam_pengiriman')
                ->where('tb_pengiriman.id_staff', $staffId)
                ->select(
                    'tb_pengiriman.id_pengiriman',
                    'tb_pengiriman.id_staff',
                    'tb_pengiriman.id_address',
                    'tb_pengiriman.id_transaksi',
                    'tb_pengiriman.no_resi',
                    'tb_pengiriman.nama_penerima',
                    'tb_pengiriman.alamat_penerima',
                    'tb_pengiriman.status_pengiriman',
                    'tb_pengiriman.tgl_kirim',
                    'tb_transaksi.kode_transaksi as kode_transaksi_full',
                    'tb_transaksi.id_pelanggan',
                    'tb_transaksi.tanggal_transaksi',
                    'tb_pelanggan.nama_pelanggan',
                    DB::raw('tb_pengiriman.id_staff as id_kurir'),
                    'tb_cabang.nama_cabang',
                    'tb_cabang.alamat as alamat_cabang',
                    'tb_cabang.no_telepon as telp_cabang',
                    'customer_addresses.alamat_lengkap',
                    'customer_addresses.kota',
                    'customer_addresses.kecamatan',
                    'customer_addresses.kode_pos',
                    'customer_addresses.latitude',
                    'customer_addresses.longitude',
                    'customer_addresses.nama_penerima as nama_penerima_address',
                    'customer_addresses.no_telp_penerima'
                )
                ->orderBy('tb_pengiriman.id_pengiriman', 'desc')
                ->get();

            // Get completed orders - Last 30 days (not just today)
            // Changed from today() to last 30 days to show more data
            $completedOrders = DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
                ->leftJoin('tb_cabang', 'tb_transaksi.id_cabang', '=', 'tb_cabang.id_cabang')
                ->leftJoin('customer_addresses', 'tb_pengiriman.id_address', '=', 'customer_addresses.id')
                ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                ->where('tb_pengiriman.status_pengiriman', 'selesai')
                ->where('tb_pengiriman.id_staff', $staffId)
                ->where('tb_pengiriman.tgl_sampai', '>=', now()->subDays(30))
                ->select(
                    'tb_pengiriman.id_pengiriman',
                    'tb_pengiriman.id_staff',
                    'tb_pengiriman.id_address',
                    'tb_pengiriman.id_transaksi',
                    'tb_pengiriman.no_resi',
                    'tb_pengiriman.nama_penerima',
                    'tb_pengiriman.alamat_penerima',
                    'tb_pengiriman.status_pengiriman',
                    'tb_pengiriman.tgl_sampai',
                    'tb_transaksi.kode_transaksi as kode_transaksi_full',
                    'tb_transaksi.id_pelanggan',
                    'tb_pelanggan.nama_pelanggan',
                    'tb_cabang.nama_cabang',
                    'tb_cabang.alamat as alamat_cabang',
                    'tb_cabang.no_telepon as telp_cabang',
                    'customer_addresses.alamat_lengkap',
                    'customer_addresses.kota',
                    'customer_addresses.kecamatan',
                    'customer_addresses.kode_pos',
                    'customer_addresses.latitude',
                    'customer_addresses.longitude',
                    'customer_addresses.nama_penerima as nama_penerima_address',
                    'customer_addresses.no_telp_penerima'
                )
                ->orderBy('tb_pengiriman.tgl_sampai', 'desc')
                ->get();

            Log::info("Completed Orders Query Result", [
                'count' => $completedOrders->count(),
                'user_id' => $user->id_user,
            ]);

            // Debug logging
            Log::info('Kurir Dashboard Data', [
                'user_id' => $user->id,
                'pendingDeliveries' => $pendingDeliveries,
                'ongoingDeliveries' => $ongoingDeliveries,
                'completedToday' => $completedToday,
                'totalDeliveries' => $totalDeliveries,
                'pendingOrders_count' => $pendingOrders->count(),
                'ongoingOrders_count' => $ongoingOrders->count(),
                'completedOrders_count' => $completedOrders->count(),
            ]);

            return view('kurir.dashboard', compact(
                'pendingDeliveries',
                'ongoingDeliveries',
                'completedToday',
                'totalDeliveries',
                'pendingOrders',
                'ongoingOrders',
                'completedOrders',
                'staffId'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading kurir dashboard: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return dashboard with default values if error occurs
            return view('kurir.dashboard', [
                'pendingDeliveries' => 0,
                'ongoingDeliveries' => 0,
                'completedToday' => 0,
                'totalDeliveries' => 0,
                'pendingOrders' => collect([]),
                'ongoingOrders' => collect([]),
                'completedOrders' => collect([]),
                'staffId' => null
            ]);
        }
    }

    /**
     * Kurir mengambil/claim pengiriman untuk dikirim
     *
     * This method handles two scenarios:
     * 1. Kurir mengambil pengiriman yang belum di-assign (id_kurir = NULL)
     * 2. Kurir memulai pengiriman yang sudah di-assign kepadanya
     */
    public function claimDelivery($id)
    {
        try {
            $user = Auth::user();

            // Verify user has kurir role (id_role = 4)
            if ($user->id_role != 4) {
                return redirect()->back()->with('error', 'Hanya kurir yang dapat mengambil pengiriman');
            }

            // Get staff record for this user (kurir must have staff record)
            $staff = DB::table('tb_staff')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$staff) {
                return redirect()->back()->with('error', 'Data staff kurir tidak ditemukan');
            }

            // Get pengiriman with transaction to check payment status
            $pengiriman = DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->where('tb_pengiriman.id_pengiriman', $id)
                ->select(
                    'tb_pengiriman.id_pengiriman',
                    'tb_pengiriman.id_staff',
                    'tb_pengiriman.id_transaksi',
                    'tb_pengiriman.status_pengiriman',
                    'tb_pengiriman.tgl_kirim',
                    'tb_transaksi.status_pembayaran'
                )
                ->first();

            if (!$pengiriman) {
                return redirect()->back()->with('error', 'Pengiriman tidak ditemukan');
            }

            // Check if payment is completed
            if ($pengiriman->status_pembayaran !== 'sudah_bayar') {
                return redirect()->back()->with('error', 'Pengiriman hanya bisa diambil setelah pembayaran lunas');
            }

            // Check if already assigned to another kurir
            if ($pengiriman->id_staff && $pengiriman->id_staff != $staff->id_staff) {
                return redirect()->back()->with('error', 'Pengiriman sudah diambil oleh kurir lain');
            }

            // Check if status is appropriate (dikemas or siap_diambil)
            if (!in_array($pengiriman->status_pengiriman, ['dikemas', 'siap_diambil'])) {
                return redirect()->back()->with('error', 'Pengiriman tidak dalam status yang dapat diambil. Status saat ini: ' . $pengiriman->status_pengiriman);
            }

            // Determine message based on whether pengiriman was already assigned or not
            $wasAlreadyAssigned = !empty($pengiriman->id_staff);

            // Debug log
            Log::info('Claiming delivery debug', [
                'staff_id_staff' => $staff->id_staff,
                'staff_id_user' => $staff->id_user,
                'user_id' => $user->id_user,
                'pengiriman_id' => $id
            ]);

            // Assign kurir and update status to dalam_pengiriman
            // Use staff.id_staff instead of user.id_user (foreign key requirement)
            DB::table('tb_pengiriman')
                ->where('id_pengiriman', $id)
                ->update([
                    'id_staff' => $staff->id_staff,
                    'status_pengiriman' => 'dalam_pengiriman',
                    'tgl_kirim' => now()
                ]);

            // Update transaction status
            DB::table('tb_transaksi')
                ->where('id_transaksi', $pengiriman->id_transaksi)
                ->update([
                    'status_pengiriman' => 'dikirim'
                ]);

            Log::info('Kurir claimed delivery and started delivery', [
                'kurir_id' => $user->id,
                'kurir_name' => $user->name,
                'pengiriman_id' => $id,
                'was_already_assigned' => $wasAlreadyAssigned,
                'status' => 'dalam_pengiriman'
            ]);

            $message = $wasAlreadyAssigned
                ? 'Pengiriman berhasil dimulai. Status diubah menjadi Dalam Pengiriman.'
                : 'Berhasil mengambil dan memulai pengiriman. Status diubah menjadi "Dalam Pengiriman".';

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error claiming delivery: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil pengiriman: ' . $e->getMessage());
        }
    }

    /**
     * Kurir menandai pengiriman selesai
     */
    public function completeDelivery($id)
    {
        try {
            $user = Auth::user();

            // Verify user has kurir role (id_role = 4)
            if ($user->id_role != 4) {
                return redirect()->back()->with('error', 'Hanya kurir yang dapat menyelesaikan pengiriman');
            }

            // Get staff record for this user (kurir must have staff record)
            $staff = DB::table('tb_staff')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$staff) {
                return redirect()->back()->with('error', 'Data staff kurir tidak ditemukan');
            }

            // Get pengiriman
            $pengiriman = DB::table('tb_pengiriman')->where('id_pengiriman', $id)->first();

            if (!$pengiriman) {
                return redirect()->back()->with('error', 'Pengiriman tidak ditemukan');
            }

            // Check if assigned to this kurir (compare id_staff, not id_user)
            if ($pengiriman->id_staff != $staff->id_staff) {
                return redirect()->back()->with('error', 'Anda tidak berhak menyelesaikan pengiriman ini');
            }

            // Check if status is dalam_pengiriman
            if ($pengiriman->status_pengiriman !== 'dalam_pengiriman') {
                return redirect()->back()->with('error', 'Pengiriman harus dalam status "Dalam Pengiriman" untuk dapat diselesaikan');
            }

            // Update status to completed
            DB::table('tb_pengiriman')
                ->where('id_pengiriman', $id)
                ->update([
                    'status_pengiriman' => 'selesai', // FIXED: Changed from 'terkirim' to 'selesai'
                    'tgl_sampai' => now()
                ]);

            // Update transaction status
            DB::table('tb_transaksi')
                ->where('id_transaksi', $pengiriman->id_transaksi)
                ->update([
                    'status_pengiriman' => 'selesai' // FIXED: Changed from 'sampai' to 'selesai'
                ]);

            Log::info('Delivery completed', [
                'kurir_id' => $user->id,
                'kurir_name' => $user->name,
                'pengiriman_id' => $id
            ]);

            return redirect()->back()->with('success', 'Pengiriman berhasil diselesaikan!');

        } catch (\Exception $e) {
            Log::error('Error completing delivery: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyelesaikan pengiriman: ' . $e->getMessage());
        }
    }

    /**
     * Display shipping management page
     */
    public function manageShipping()
    {
        try {
            // Get all shipments with paid transactions
            // Include all shipping statuses to ensure data visibility
            $shipments = DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
                ->leftJoin('users as pelanggan_user', 'tb_pelanggan.id_user', '=', 'pelanggan_user.id_user')
                ->leftJoin('tb_staff', 'tb_pengiriman.id_staff', '=', 'tb_staff.id_staff')
                ->leftJoin('users as staff_user', 'tb_staff.id_user', '=', 'staff_user.id_user')
                ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                ->select(
                    'tb_pengiriman.id_pengiriman',
                    'tb_pengiriman.id_address',
                    'tb_pengiriman.id_transaksi',
                    'tb_pengiriman.id_staff',
                    'tb_pengiriman.no_resi',
                    'tb_pengiriman.nama_penerima',
                    'tb_pengiriman.alamat_penerima',
                    'tb_pengiriman.status_pengiriman',
                    'tb_pengiriman.tgl_kirim',
                    'tb_pengiriman.tgl_sampai',
                    'tb_transaksi.id_pelanggan',
                    'tb_transaksi.kode_transaksi as kode_transaksi_full',
                    'tb_transaksi.total_harga',
                    'tb_transaksi.status_pembayaran',
                    'tb_pelanggan.nama_pelanggan',
                    'pelanggan_user.email as email_pelanggan',
                    'tb_staff.nama_staff',
                    'staff_user.email as email_kurir',
                    'staff_user.id_user as id_kurir' // For backward compatibility
                )
                ->orderBy('tb_pengiriman.tgl_kirim', 'desc')
                ->paginate(20);

            // Get available couriers (staff with kurir role)
            $couriers = DB::table('tb_staff')
                ->join('users', 'tb_staff.id_user', '=', 'users.id_user')
                ->where('users.id_role', 4) // Kurir role
                ->where('tb_staff.status_akun', 'aktif')
                ->select(
                    'tb_staff.id_staff as id',
                    'tb_staff.nama_staff as name',
                    'tb_staff.email_staff as email',
                    'tb_staff.no_tlp_staff as phone'
                )
                ->get();

            // Count shipments by status
            $statusCounts = [
                'unassigned' => DB::table('tb_pengiriman')
                    ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                    ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                    ->whereNull('tb_pengiriman.id_staff')
                    ->count(),

                'assigned' => DB::table('tb_pengiriman')
                    ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                    ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                    ->whereNotNull('tb_pengiriman.id_staff')
                    ->whereIn('tb_pengiriman.status_pengiriman', ['siap_diambil', 'dikemas'])
                    ->count(),

                'in_transit' => DB::table('tb_pengiriman')
                    ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                    ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                    ->where('tb_pengiriman.status_pengiriman', 'dalam_pengiriman')
                    ->count(),

                'delivered' => DB::table('tb_pengiriman')
                    ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                    ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
                    ->where('tb_pengiriman.status_pengiriman', 'terkirim')
                    ->count(),
            ];

            return view('kurir.shipping.index', compact('shipments', 'couriers', 'statusCounts'));
        } catch (\Exception $e) {
            Log::error('Error loading shipping management: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data pengiriman.');
        }
    }

    /**
     * Assign courier to a shipment
     */
    public function assignCourier(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'id_staff' => 'required|exists:tb_staff,id_staff',
            ]);

            // Get shipment with transaction
            $shipment = DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->where('tb_pengiriman.id_pengiriman', $id)
                ->select('tb_pengiriman.*', 'tb_transaksi.status_pembayaran')
                ->first();

            if (!$shipment) {
                return back()->with('error', 'Pengiriman tidak ditemukan.');
            }

            // Check if transaction is paid
            if ($shipment->status_pembayaran !== 'sudah_bayar') {
                return back()->with('error', 'Tidak dapat assign kurir. Pembayaran belum lunas.');
            }

            // Verify courier exists and has correct role
            $staff = DB::table('tb_staff')
                ->join('users', 'tb_staff.id_user', '=', 'users.id_user')
                ->where('tb_staff.id_staff', $validated['id_staff'])
                ->where('users.id_role', 4) // Kurir role
                ->select('tb_staff.*', 'users.id_role')
                ->first();

            if (!$staff) {
                return back()->with('error', 'Kurir yang dipilih tidak valid.');
            }

            // Assign courier
            DB::table('tb_pengiriman')
                ->where('id_pengiriman', $id)
                ->update([
                    'id_staff' => $validated['id_staff'],
                    'status_pengiriman' => 'siap_diambil', // Ready for courier to pick up
                ]);

            Log::info('Courier assigned to shipment', [
                'shipment_id' => $id,
                'staff_id' => $validated['id_staff'],
                'staff_name' => $staff->nama_staff,
                'transaction_id' => $shipment->id_transaksi,
                'assigned_by' => Auth::user()->email
            ]);

            return back()->with('success', "Kurir {$staff->nama_staff} berhasil di-assign ke pengiriman ini!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error assigning courier: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat meng-assign kurir.');
        }
    }

    /**
     * Unassign courier from a shipment
     */
    public function unassignCourier($id)
    {
        try {
            // Get shipment
            $shipment = DB::table('tb_pengiriman')
                ->where('id_pengiriman', $id)
                ->first();

            if (!$shipment) {
                return back()->with('error', 'Pengiriman tidak ditemukan.');
            }

            // Cannot unassign if already in delivery or completed
            if (in_array($shipment->status_pengiriman, ['dalam_pengiriman', 'terkirim', 'selesai'])) {
                return back()->with('error', 'Tidak dapat unassign. Pengiriman sudah dalam proses atau selesai.');
            }

            // Get staff name before unassigning
            $staff = DB::table('tb_staff')->where('id_staff', $shipment->id_staff)->first();
            $staffName = $staff ? $staff->nama_staff : 'Unknown';

            // Unassign courier
            DB::table('tb_pengiriman')
                ->where('id_pengiriman', $id)
                ->update([
                    'id_staff' => null,
                    'status_pengiriman' => 'dikemas', // Back to packing status
                ]);

            Log::info('Courier unassigned from shipment', [
                'shipment_id' => $id,
                'staff_name' => $staffName,
                'transaction_id' => $shipment->id_transaksi,
                'unassigned_by' => Auth::user()->email
            ]);

            return back()->with('success', "Kurir {$staffName} berhasil di-unassign dari pengiriman ini!");

        } catch (\Exception $e) {
            Log::error('Error unassigning courier: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat meng-unassign kurir.');
        }
    }
}
