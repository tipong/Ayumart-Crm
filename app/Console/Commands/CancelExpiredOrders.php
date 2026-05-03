<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel orders that have not been paid within 15 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for expired unpaid orders...');

        try {
            // Find unpaid orders that have expired (past payment_expired_at time)
            $expiredOrders = Order::where('status_pembayaran', 'belum_bayar')
                ->whereNotNull('payment_expired_at')
                ->where('payment_expired_at', '<', now())
                ->get();

            if ($expiredOrders->isEmpty()) {
                $this->info('✅ No expired orders found.');
                Log::info('✅ CancelExpiredOrders: No expired orders found');
                return 0;
            }

            $cancelledCount = 0;
            $errorCount = 0;

            foreach ($expiredOrders as $order) {
                try {
                    $this->info("⏳ Processing order: {$order->kode_transaksi}");

                    // Update order status to kadaluarsa
                    $order->status_pembayaran = 'kadaluarsa';
                    $order->save();

                    // Restore stock ke database integrasi (jika pernah dikurangi)
                    $this->restoreStockAfterCancellation($order);

                    Log::info('✅ Order automatically cancelled due to payment timeout', [
                        'order_id' => $order->id_transaksi,
                        'order_code' => $order->kode_transaksi,
                        'payment_expired_at' => $order->payment_expired_at,
                        'cancelled_at' => now(),
                    ]);

                    $cancelledCount++;
                    $this->info("✅ Cancelled: {$order->kode_transaksi}");

                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('❌ Error cancelling expired order', [
                        'order_id' => $order->id_transaksi,
                        'order_code' => $order->kode_transaksi,
                        'error' => $e->getMessage(),
                    ]);
                    $this->error("❌ Error: {$order->kode_transaksi} - {$e->getMessage()}");
                }
            }

            $this->info("\n📊 Summary:");
            $this->info("✅ Cancelled: {$cancelledCount} orders");
            $this->error("❌ Errors: {$errorCount} orders");

            Log::info('✅ CancelExpiredOrders completed', [
                'total_expired' => $expiredOrders->count(),
                'cancelled' => $cancelledCount,
                'errors' => $errorCount,
            ]);

            return 0;

        } catch (\Exception $e) {
            Log::error('❌ CancelExpiredOrders command error: ' . $e->getMessage());
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Restore stock after order cancellation
     */
    private function restoreStockAfterCancellation($order)
    {
        try {
            $integrasiService = app(\App\Services\IntegrasiProdukService::class);

            // Get order items
            $orderItems = DB::table('tb_detail_transaksi')
                ->where('id_transaksi', $order->id_transaksi)
                ->get();

            foreach ($orderItems as $item) {
                // Restore stock ke database integrasi
                if ($order->id_cabang) {
                    $integrasiService->tambahStok(
                        $item->id_produk,
                        $order->id_cabang,
                        $item->qty
                    );

                    Log::info('Stock restored after order cancellation', [
                        'order_id' => $order->id_transaksi,
                        'product_id' => $item->id_produk,
                        'qty' => $item->qty,
                        'branch_id' => $order->id_cabang,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error restoring stock after cancellation: ' . $e->getMessage());
        }
    }
}
