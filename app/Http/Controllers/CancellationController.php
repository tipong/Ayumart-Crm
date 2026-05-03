<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\PembatalanTransaksi;
use App\Services\IntegrasiProdukService;

class CancellationController extends Controller
{
    /**
     * Request cancellation by customer (only for unpaid orders)
     */
    public function requestCancellation(Request $request, $orderId)
    {
        try {
            $user = Auth::user();
            $pelanggan = $user->getOrCreatePelanggan();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan tidak ditemukan'
                ], 404);
            }

            $order = Order::where('id_transaksi', $orderId)
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            // Check if order is already paid
            if ($order->status_pembayaran === 'sudah_bayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi yang sudah dibayar tidak dapat dibatalkan'
                ], 400);
            }

            // Check if cancellation already exists
            $existingCancellation = PembatalanTransaksi::where('id_transaksi', $orderId)->first();
            if ($existingCancellation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan pembatalan sudah diajukan sebelumnya'
                ], 400);
            }

            $validated = $request->validate([
                'alasan_pembatalan' => 'required|string|max:500',
            ]);

            DB::beginTransaction();

            // Create cancellation request
            PembatalanTransaksi::create([
                'id_transaksi' => $orderId,
                'alasan_pembatalan' => $validated['alasan_pembatalan'],
                'status_pembatalan' => 'diajukan', // Waiting for admin approval
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan pembatalan transaksi berhasil diajukan. Menunggu konfirmasi admin.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error requesting cancellation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengajukan pembatalan'
            ], 500);
        }
    }

    /**
     * Admin approve or reject cancellation
     */
    public function processCancellation(Request $request, PembatalanTransaksi $cancellation)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'catatan_admin' => 'nullable|string|max:500',
            ]);

            // Load relationships if not already loaded
            $cancellation->load(['transaksi.pelanggan', 'transaksi.details.product']);

            if ($cancellation->status_pembatalan !== 'diajukan') {
                return back()->with('error', 'Permintaan pembatalan sudah diproses sebelumnya');
            }

            DB::beginTransaction();

            if ($validated['action'] === 'approve') {
                // Approve cancellation
                $cancellation->update([
                    'status_pembatalan' => 'disetujui',
                    'catatan_admin' => $validated['catatan_admin'] ?? null,
                    'diproses_oleh' => Auth::id(),
                ]);

                // Update transaction status
                $cancellation->transaksi->update([
                    'status_pembayaran' => 'kadaluarsa', // Mark as cancelled
                ]);

                // Restore product stock di database integrasi
                if ($cancellation->transaksi->id_cabang) {
                    $integrasiService = app(IntegrasiProdukService::class);

                    foreach ($cancellation->transaksi->details as $item) {
                        if ($item->product) {
                            try {
                                $integrasiService->tambahStok(
                                    $item->id_produk,
                                    $cancellation->transaksi->id_cabang,
                                    $item->qty
                                );
                                Log::info('Stock restored in integrasi DB for product: ' . $item->product->nama_produk . ', qty: ' . $item->qty);
                            } catch (\Exception $e) {
                                Log::error('Failed to restore stock in integrasi DB: ' . $e->getMessage());
                            }
                        }
                    }
                }

                DB::commit();
                return back()->with('success', 'Pembatalan transaksi disetujui dan stok produk telah dikembalikan');

            } else {
                // Reject cancellation
                $cancellation->update([
                    'status_pembatalan' => 'ditolak',
                    'catatan_admin' => $validated['catatan_admin'] ?? null,
                    'diproses_oleh' => Auth::id(),
                ]);

                DB::commit();
                return back()->with('success', 'Permintaan pembatalan transaksi ditolak');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error in processCancellation: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing cancellation: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan saat memproses pembatalan: ' . $e->getMessage());
        }
    }

    /**
     * Show cancellation list for admin
     */
    public function index()
    {
        try {
            $cancellations = PembatalanTransaksi::with(['transaksi.pelanggan'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $statusCounts = [
                'pending' => PembatalanTransaksi::where('status_pembatalan', 'diajukan')->count(),
                'approved' => PembatalanTransaksi::where('status_pembatalan', 'disetujui')->count(),
                'rejected' => PembatalanTransaksi::where('status_pembatalan', 'ditolak')->count(),
            ];

            return view('admin.cancellations.index', compact('cancellations', 'statusCounts'));
        } catch (\Exception $e) {
            Log::error('Error loading cancellations: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pembatalan');
        }
    }

    /**
     * Show cancellation detail for admin
     */
    public function show(PembatalanTransaksi $cancellation)
    {
        try {
            // Load relationships
            $cancellation->load(['transaksi.pelanggan', 'transaksi.details.product', 'admin']);

            return view('admin.cancellations.show', compact('cancellation'));
        } catch (\Exception $e) {
            Log::error('Error loading cancellation detail: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan saat memuat detail pembatalan: ' . $e->getMessage());
        }
    }
}
