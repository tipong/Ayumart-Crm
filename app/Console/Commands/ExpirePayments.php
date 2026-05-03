<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExpirePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire unpaid transactions after payment deadline (10 minutes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired payments...');

        try {
            // Find all transactions that should be expired
            $expiredCount = DB::table('tb_transaksi')
                ->where('status_pembayaran', 'belum_bayar')
                ->where('payment_expired_at', '<=', Carbon::now())
                ->whereNotNull('payment_expired_at')
                ->update([
                    'status_pembayaran' => 'kadaluarsa',
                    'updated_at' => Carbon::now()
                ]);

            if ($expiredCount > 0) {
                $this->info("Successfully expired {$expiredCount} payment(s)");

                Log::info('Payments Expired', [
                    'count' => $expiredCount,
                    'timestamp' => Carbon::now()
                ]);

                // Get expired transaction codes for logging
                $expiredTransactions = DB::table('tb_transaksi')
                    ->where('status_pembayaran', 'kadaluarsa')
                    ->where('updated_at', '>=', Carbon::now()->subMinutes(1))
                    ->pluck('kode_transaksi')
                    ->toArray();

                if (!empty($expiredTransactions)) {
                    Log::info('Expired Transaction Codes', [
                        'codes' => $expiredTransactions
                    ]);
                }

                // Optional: Cancel Midtrans transactions
                $this->cancelMidtransTransactions($expiredTransactions);
            } else {
                $this->info('No payments to expire');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error expiring payments: ' . $e->getMessage());
            Log::error('Payment Expiry Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Cancel expired transactions in Midtrans
     */
    private function cancelMidtransTransactions(array $transactionCodes)
    {
        if (empty($transactionCodes)) {
            return;
        }

        // Configure Midtrans
        try {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

            foreach ($transactionCodes as $code) {
                try {
                    // Try to cancel in Midtrans
                    \Midtrans\Transaction::cancel($code);

                    $this->info("Cancelled Midtrans transaction: {$code}");
                    Log::info('Midtrans Transaction Cancelled', ['code' => $code]);

                } catch (\Exception $e) {
                    // Transaction might already be expired or not found in Midtrans
                    // This is okay, just log it
                    Log::warning('Failed to cancel Midtrans transaction (may already be expired)', [
                        'code' => $code,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Midtrans Config Error in ExpirePayments', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
