<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        // Get year from request or use current year
        $currentYear = $request->get('year', now()->year);

        // Get revenue statistics (excluding cancelled)
        $totalRevenue = Order::where('status_pembayaran', 'sudah_bayar')
            ->whereDoesntHave('cancellation', function($q) {
                $q->where('status_pembatalan', 'disetujui');
            })
            ->get()
            ->map(function($t) { return $t->getTotalAmount(); })
            ->sum();

        // Get transaction statistics (excluding cancelled)
        $totalTransactions = Order::whereDoesntHave('cancellation', function($q) {
            $q->where('status_pembatalan', 'disetujui');
        })->count();

        // Get customer statistics
        $totalCustomers = Pelanggan::count();

        // Get product statistics (Total item yang terjual, bukan jumlah SKU)
        $totalProducts = DB::table('tb_detail_transaksi as dt')
            ->join('tb_transaksi as t', 'dt.id_transaksi', '=', 't.id_transaksi')
            ->leftJoin('tb_pembatalan_transaksi as pt', 't.id_transaksi', '=', 'pt.id_transaksi')
            ->where('t.status_pembayaran', 'sudah_bayar')
            ->where(function($query) {
                $query->whereNull('pt.id_pembatalan_transaksi')
                      ->orWhere('pt.status_pembatalan', '!=', 'disetujui');
            })
            ->sum('dt.qty');

        // Get monthly sales data for the selected year
        $salesData = array_fill(0, 12, 0); // Default all months to 0

        // Query sales data grouped by month using tanggal_transaksi column (excluding cancelled)
        $monthlySales = Order::where('status_pembayaran', 'sudah_bayar')
            ->whereDoesntHave('cancellation', function($q) {
                $q->where('status_pembatalan', 'disetujui');
            })
            ->whereYear('tanggal_transaksi', $currentYear)
            ->selectRaw('MONTH(tanggal_transaksi) as month, ROUND(SUM(total_harga - total_diskon + ongkir + COALESCE(biaya_membership, 0)) / 1000000, 2) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Map monthly sales to array (convert to millions for display)
        foreach ($monthlySales as $sale) {
            $salesData[$sale->month - 1] = $sale->total;
        }

        // Get all paid orders and their totals (excluding cancelled)
        $totalSales = Order::where('status_pembayaran', 'sudah_bayar')
            ->whereDoesntHave('cancellation', function($q) {
                $q->where('status_pembatalan', 'disetujui');
            })
            ->sum('total_harga');

        // Get top products from detail transaksi with paid orders
        // Get top products from detail transaksi with paid orders (excluding cancelled & filtered by year)
        $topProductIds = DB::table('tb_detail_transaksi as dt')
            ->join('tb_transaksi as t', 'dt.id_transaksi', '=', 't.id_transaksi')
            ->leftJoin('tb_pembatalan_transaksi as pt', 't.id_transaksi', '=', 'pt.id_transaksi')
            ->where('t.status_pembayaran', 'sudah_bayar')
            ->whereYear('t.tanggal_transaksi', $currentYear)
            ->where(function($query) {
                $query->whereNull('pt.id_pembatalan_transaksi')
                      ->orWhere('pt.status_pembatalan', '!=', 'disetujui');
            })
            ->selectRaw('dt.id_produk, SUM(dt.qty) as total_sold')
            ->groupBy('dt.id_produk')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Step 2: Get product names from integrasi database
        $topProductLabels = [];
        $topProductData = [];

        if ($topProductIds->count() > 0) {
            $productIds = $topProductIds->pluck('id_produk')->toArray();

            // Get product details from integrasi database
            $products = DB::connection('mysql_integrasi')
                ->table('tb_produk')
                ->whereIn('id_produk', $productIds)
                ->select('id_produk', 'nama_produk')
                ->get()
                ->keyBy('id_produk');

            // Map the data with product names
            foreach ($topProductIds as $item) {
                $product = $products[$item->id_produk] ?? null;
                if ($product) {
                    $topProductLabels[] = $product->nama_produk;
                    $topProductData[] = (int)$item->total_sold;
                }
            }
        }

        // Get latest transactions using Eloquent with relationships
        $latestTransactions = Order::with('pelanggan')
            ->orderBy('id_transaksi', 'desc')
            ->limit(10)
            ->get();

        return view('owner.dashboard', compact(
            'totalRevenue',
            'totalTransactions',
            'totalCustomers',
            'totalProducts',
            'salesData',
            'topProductLabels',
            'topProductData',
            'latestTransactions',
            'currentYear'
        ));
    }

    public function laporanPenjualan()
    {
        // Get all branches for filter dropdown
        $branches = Cabang::where('is_active', true)
            ->orderBy('nama_cabang')
            ->get();

        return view('owner.laporan', compact('branches'));
    }

    public function downloadLaporanPerCabang(Request $request)
    {
        $request->validate([
            'id_cabang' => 'required|exists:tb_cabang,id_cabang',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:' . (now()->year + 1),
        ]);

        $idCabang = $request->id_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Nama bulan dalam Bahasa Indonesia
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Get branch information
        $cabang = Cabang::findOrFail($idCabang);

        // Get transactions for the selected month and branch using Transaction model
        $transactions = Transaction::with(['pelanggan.user', 'cabang', 'details'])
            ->where('id_cabang', $idCabang)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun)
            ->where('status_pembayaran', 'sudah_bayar')
            ->whereDoesntHave('cancellation', function($q) {
                $q->where('status_pembatalan', 'disetujui');
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Calculate statistics
        $totalPenjualan = $transactions->sum(function($t) {
            return $t->total_harga + $t->details->sum('diskon_item');
        });
        $totalDiskon = $transactions->sum(function($t) {
            return $t->total_diskon + $t->details->sum('diskon_item');
        });
        $totalOngkir = $transactions->sum('ongkir');
        $totalBiayaMember = $transactions->sum('biaya_membership');
        $totalTransaksi = $transactions->count();
        $pendapatanBersih = $totalPenjualan - $totalDiskon;
        $grandTotal = $transactions->map(function($t) { return $t->getTotalAmount(); })->sum();

        // Get top products for the month at this branch
        $topProducts = DB::connection('mysql')
            ->table('tb_detail_transaksi')
            ->join('tb_transaksi', 'tb_detail_transaksi.id_transaksi', '=', 'tb_transaksi.id_transaksi')
            ->join('db_integrasi_ayu_mart.tb_produk', 'tb_detail_transaksi.id_produk', '=', 'db_integrasi_ayu_mart.tb_produk.id_produk')
            ->leftJoin('tb_pembatalan_transaksi', 'tb_transaksi.id_transaksi', '=', 'tb_pembatalan_transaksi.id_transaksi')
            ->where('tb_transaksi.id_cabang', $idCabang)
            ->whereMonth('tb_transaksi.tanggal_transaksi', $bulan)
            ->whereYear('tb_transaksi.tanggal_transaksi', $tahun)
            ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
            ->where(function($query) {
                $query->whereNull('tb_pembatalan_transaksi.id_pembatalan_transaksi')
                      ->orWhere('tb_pembatalan_transaksi.status_pembatalan', '!=', 'disetujui');
            })
            ->select(
                'db_integrasi_ayu_mart.tb_produk.nama_produk',
                DB::raw('SUM(tb_detail_transaksi.qty) as total_terjual'),
                DB::raw('SUM(tb_detail_transaksi.subtotal) as total_pendapatan')
            )
            ->groupBy('db_integrasi_ayu_mart.tb_produk.id_produk', 'db_integrasi_ayu_mart.tb_produk.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();

        // Prepare data for PDF
        $data = [
            'cabang' => $cabang,
            'bulan' => $namaBulan[$bulan],
            'tahun' => $tahun,
            'transactions' => $transactions,
            'totalPenjualan' => $totalPenjualan,
            'totalDiskon' => $totalDiskon,
            'totalOngkir' => $totalOngkir,
            'totalBiayaMember' => $totalBiayaMember,
            'totalTransaksi' => $totalTransaksi,
            'pendapatanBersih' => $pendapatanBersih,
            'grandTotal' => $grandTotal,
            'topProducts' => $topProducts,
            'tanggalCetak' => now()->format('d/m/Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('owner.laporan-cabang-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        // Download PDF
        $filename = 'Laporan_Penjualan_' . $cabang->nama_cabang . '_' . $namaBulan[$bulan] . '_' . $tahun . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadLaporan(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:' . (now()->year + 1),
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Nama bulan dalam Bahasa Indonesia
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Get transactions for the selected period
        $transactions = Order::with(['pelanggan', 'details.product'])
            ->whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun)
            ->where('status_pembayaran', 'sudah_bayar')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Calculate statistics
        $totalPenjualan = $transactions->sum(function($t) {
            return $t->total_harga + $t->details->sum('diskon_item');
        });
        $totalDiskon = $transactions->sum(function($t) {
            return $t->total_diskon + $t->details->sum('diskon_item');
        });
        $totalOngkir = $transactions->sum('ongkir');
        $totalBiayaMember = $transactions->sum('biaya_membership');
        $totalTransaksi = $transactions->count();
        $pendapatanBersih = $totalPenjualan - $totalDiskon;
        $grandTotal = $transactions->map(function($t) { return $t->getTotalAmount(); })->sum();

        // Get top products for the selected month
        $topProducts = DB::table('tb_detail_transaksi')
            ->join('tb_produk', 'tb_detail_transaksi.id_produk', '=', 'tb_produk.id_produk')
            ->join('tb_transaksi', 'tb_detail_transaksi.id_transaksi', '=', 'tb_transaksi.id_transaksi')
            ->whereMonth('tb_transaksi.tanggal_transaksi', $bulan)
            ->whereYear('tb_transaksi.tanggal_transaksi', $tahun)
            ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
            ->select(
                'tb_produk.nama_produk',
                DB::raw('SUM(tb_detail_transaksi.qty) as total_terjual'),
                DB::raw('SUM(tb_detail_transaksi.subtotal) as total_pendapatan')
            )
            ->groupBy('tb_produk.id_produk', 'tb_produk.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();

        // Prepare data for PDF
        $data = [
            'bulan' => $namaBulan[$bulan],
            'tahun' => $tahun,
            'transactions' => $transactions,
            'totalPenjualan' => $totalPenjualan,
            'totalDiskon' => $totalDiskon,
            'totalOngkir' => $totalOngkir,
            'totalBiayaMember' => $totalBiayaMember,
            'totalTransaksi' => $totalTransaksi,
            'pendapatanBersih' => $pendapatanBersih,
            'grandTotal' => $grandTotal,
            'topProducts' => $topProducts,
            'tanggalCetak' => now()->format('d/m/Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('owner.laporan-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        // Download PDF
        $filename = 'Laporan_Penjualan_' . $namaBulan[$bulan] . '_' . $tahun . '.pdf';
        return $pdf->download($filename);
    }

    public function transactions(Request $request)
    {
        try {
            // Dynamically check and cancel expired orders first
            Order::checkAndCancelExpiredOrders();

            $search = $request->get('search', '');
            $statusFilter = $request->get('status', '');
            $branchFilter = $request->get('branch', '');
            $shippingFilter = $request->get('shipping_method', '');
            $startDate = $request->get('start_date', '');
            $endDate = $request->get('end_date', '');
            $sortBy = $request->get('sort_by', 'date_desc');
            $perPage = 15;

            // Build base query
            $query = Order::with(['pelanggan.user', 'cancellation', 'cabang']);

            // Apply search
            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $query = $query->where(function ($q) use ($searchTerm) {
                    $q->where('kode_transaksi', 'like', $searchTerm)
                      ->orWhereHas('pelanggan', function ($subQ) use ($searchTerm) {
                          $subQ->where('nama_pelanggan', 'like', $searchTerm);
                      })
                      ->orWhereHas('cabang', function ($subQ) use ($searchTerm) {
                          $subQ->where('nama_cabang', 'like', $searchTerm);
                      });
                });
            }

            // Apply status filter
            if (!empty($statusFilter)) {
                $query->where('status_pembayaran', $statusFilter);
            }

            // Apply branch filter
            if (!empty($branchFilter)) {
                $query->where('id_cabang', $branchFilter);
            }

            // Apply shipping filter
            if (!empty($shippingFilter)) {
                $query->where('metode_pengiriman', $shippingFilter);
            }

            // Apply date filters
            if (!empty($startDate)) {
                $query->where('tanggal_transaksi', '>=', $startDate . ' 00:00:00');
            }
            if (!empty($endDate)) {
                $query->where('tanggal_transaksi', '<=', $endDate . ' 23:59:59');
            }

            // Apply sorting
            if ($sortBy === 'date_asc') {
                $query->orderBy('tanggal_transaksi', 'asc');
            } elseif ($sortBy === 'total_desc') {
                $query->orderByRaw('(total_harga - total_diskon + ongkir + COALESCE(biaya_membership, 0)) DESC');
            } elseif ($sortBy === 'total_asc') {
                $query->orderByRaw('(total_harga - total_diskon + ongkir + COALESCE(biaya_membership, 0)) ASC');
            } else {
                // default is date_desc
                $query->orderBy('tanggal_transaksi', 'desc');
            }

            // Paginate results
            $transactions = $query->paginate($perPage);

            // Get status counts (totals)
            $statusCounts = [
                'pending' => Order::where('status_pembayaran', 'belum_bayar')->count(),
                'completed' => Order::where('status_pembayaran', 'sudah_bayar')->count(),
                'cancelled' => Order::where('status_pembayaran', 'kadaluarsa')->count(),
            ];

            // Get all active branches for dropdown
            $branches = Cabang::where('is_active', true)->orderBy('nama_cabang')->get();

            return view('owner.transactions.index', compact(
                'transactions', 
                'statusCounts', 
                'search', 
                'statusFilter', 
                'branchFilter', 
                'shippingFilter', 
                'startDate', 
                'endDate', 
                'sortBy', 
                'branches'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading owner transactions: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data transaksi.');
        }
    }

    public function showTransaction($id)
    {
        try {
            // Dynamically check and cancel expired orders first
            Order::checkAndCancelExpiredOrders();

            $transaction = Order::with(['pelanggan.user', 'details.product', 'cancellation', 'cabang'])
                ->findOrFail($id);

            return view('owner.transactions.show', compact('transaction'));
        } catch (\Exception $e) {
            Log::error('Error loading owner transaction detail: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat detail transaksi.');
        }
    }
}
