<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\PaymentUpdateService;
use Illuminate\Console\Command;

class CheckPaymentStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:check-status {orderId? : The order ID to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and display payment status for an order, or list unpaid orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('orderId');

        if ($orderId) {
            $this->checkSingleOrder($orderId);
        } else {
            $this->listUnpaidOrders();
        }
    }

    /**
     * Check status of a single order
     */
    private function checkSingleOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->error("❌ Order not found: $orderId");
            return;
        }

        $this->info("\n📦 Order Details:");
        $this->line("ID: " . $order->id_transaksi);
        $this->line("Code: " . $order->kode_transaksi);
        $this->line("Amount: Rp " . number_format($order->total_harga, 0, ',', '.'));
        $this->line("Shipping: " . $order->metode_pengiriman);

        $statusColor = match ($order->status_pembayaran) {
            'sudah_bayar' => 'info',
            'belum_bayar' => 'error',
            'kadaluarsa' => 'warn',
            default => 'comment'
        };

        $this->line("\n💳 Payment Status:");
        $this->{$statusColor}("Status: " . $order->status_pembayaran);
        $this->line("Created: " . $order->created_at->format('Y-m-d H:i:s'));
        $this->line("Last Check: " . ($order->last_payment_check_at ? $order->last_payment_check_at->format('Y-m-d H:i:s') : 'Never'));

        if ($order->payment_expired_at) {
            $this->line("Expires: " . $order->payment_expired_at->format('Y-m-d H:i:s'));
        }

        // Verify using service
        $this->line("\n🔍 Verification:");
        $result = PaymentUpdateService::verifyPaymentStatus($orderId);

        if ($result['success']) {
            $isPaidText = $result['is_paid'] ? '✅ Paid' : '❌ Not Paid';
            $this->info($isPaidText);
        } else {
            $this->error('Verification failed: ' . $result['status'] ?? 'Unknown error');
        }

        $this->info("\n✅ Check complete\n");
    }

    /**
     * List all unpaid orders
     */
    private function listUnpaidOrders()
    {
        $unpaidOrders = Order::where('status_pembayaran', 'belum_bayar')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        if ($unpaidOrders->isEmpty()) {
            $this->info("✅ No unpaid orders found!");
            return;
        }

        $this->warn("\n⚠️  Unpaid Orders (Last 20):");
        $this->line("");

        $headers = ['ID', 'Code', 'Amount', 'Created', 'Expires'];
        $data = $unpaidOrders->map(function ($order) {
            return [
                $order->id_transaksi,
                $order->kode_transaksi,
                'Rp ' . number_format($order->total_harga, 0, ',', '.'),
                $order->created_at->format('Y-m-d H:i'),
                $order->payment_expired_at ? $order->payment_expired_at->format('Y-m-d H:i') : 'N/A'
            ];
        })->toArray();

        $this->table($headers, $data);

        $expiredCount = $unpaidOrders->filter(function ($order) {
            return $order->payment_expired_at && $order->payment_expired_at < now();
        })->count();

        if ($expiredCount > 0) {
            $this->warn("\n⚠️  $expiredCount orders have expired!");
        }

        $this->info("\n💡 Usage: payment:check-status {orderId}\n");
    }
}
