<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

/**
 * ✅ PAYMENT DATABASE UPDATE SERVICE
 *
 * Service ini memastikan status pembayaran selalu tersimpan dengan benar
 * dengan multiple layer verification dan atomic transactions
 */
class PaymentUpdateService
{
    /**
     * Update payment status dengan GUARANTEE penyimpanan ke database
     *
     * @param int $orderId - Order ID (id_transaksi)
     * @param string $newStatus - 'sudah_bayar' atau 'kadaluarsa'
     * @param array $additionalData - Data tambahan untuk di-update
     * @return array ['success' => bool, 'message' => string, 'data' => Order]
     */
    public static function updatePaymentStatus($orderId, $newStatus = 'sudah_bayar', $additionalData = [])
    {
        Log::info('🔄 PaymentUpdateService::updatePaymentStatus START', [
            'order_id' => $orderId,
            'new_status' => $newStatus,
            'additional_data' => $additionalData
        ]);

        try {
            // ========== STEP 1: Find Order ==========
            $order = Order::find($orderId);

            if (!$order) {
                Log::error('❌ Order tidak ditemukan', ['order_id' => $orderId]);
                return [
                    'success' => false,
                    'message' => 'Order tidak ditemukan',
                    'data' => null
                ];
            }

            Log::info('📦 Order ditemukan', [
                'order_id' => $order->id_transaksi,
                'order_code' => $order->kode_transaksi,
                'current_status' => $order->status_pembayaran
            ]);

            // ========== STEP 2: Prepare Update Data ==========
            $updateData = array_merge([
                'status_pembayaran' => $newStatus,
                'last_payment_check_at' => now()
            ], $additionalData);

            // CRITICAL: If payment successful, clear temporary payment tokens
            // This ensures future payment checks don't get confused by old retry payment IDs
            if ($newStatus === 'sudah_bayar') {
                $updateData['snap_token'] = null;
                $updateData['midtrans_order_id'] = $order->kode_transaksi; // Reset to original code_transaksi
            }

            Log::info('📝 Preparing update data', ['update_data' => $updateData]);

            // ========== STEP 3: Update dengan Atomic Transaction ==========
            $attempt = 0;
            $maxAttempts = 5;
            $updateSuccess = false;

            while ($attempt < $maxAttempts && !$updateSuccess) {
                $attempt++;

                try {
                    // Use transaction untuk atomicity
                    DB::beginTransaction();

                    // Direct raw update untuk ensure execution
                    $updated = DB::table('tb_transaksi')
                        ->where('id_transaksi', $orderId)
                        ->update($updateData);

                    Log::info("Update attempt $attempt - Raw query executed", [
                        'rows_affected' => $updated,
                        'attempt' => $attempt
                    ]);

                    if ($updated === 0) {
                        Log::warning("⚠️ No rows updated in attempt $attempt");
                        DB::rollBack();
                        sleep(1);
                        continue;
                    }

                    // ========== STEP 4: CRITICAL VERIFICATION ==========
                    // Verify immediately BEFORE commit
                    $verifyOrder = DB::table('tb_transaksi')
                        ->where('id_transaksi', $orderId)
                        ->first();

                    Log::info("Verifying update attempt $attempt", [
                        'expected_status' => $newStatus,
                        'actual_status' => $verifyOrder->status_pembayaran ?? 'NULL',
                        'attempt' => $attempt
                    ]);

                    // Check if update was successful
                    if ($verifyOrder && $verifyOrder->status_pembayaran === $newStatus) {
                        DB::commit();
                        $updateSuccess = true;

                        Log::info('✅ Payment status updated and verified in database', [
                            'order_id' => $orderId,
                            'order_code' => $order->kode_transaksi,
                            'new_status' => $newStatus,
                            'attempt' => $attempt,
                            'total_attempts' => $attempt
                        ]);

                    } else {
                        DB::rollBack();
                        Log::warning("⚠️ Verification failed in attempt $attempt, retrying...", [
                            'expected' => $newStatus,
                            'actual' => $verifyOrder->status_pembayaran ?? 'NULL'
                        ]);
                        sleep(1);
                    }

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("❌ Update attempt $attempt failed: " . $e->getMessage(), [
                        'order_id' => $orderId,
                        'attempt' => $attempt,
                        'error' => $e->getMessage()
                    ]);

                    if ($attempt < $maxAttempts) {
                        sleep(1);
                    }
                }
            }

            // ========== STEP 5: Final Check ==========
            if (!$updateSuccess) {
                Log::error('❌ CRITICAL: Failed to update payment status after all retries', [
                    'order_id' => $orderId,
                    'order_code' => $order->kode_transaksi,
                    'max_attempts' => $maxAttempts
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan status pembayaran ke database',
                    'data' => null
                ];
            }

            // ========== STEP 6: Clear retry payment fields after successful payment ==========
            // After successful payment, clear midtrans_order_id and snap_token to avoid confusion
            if ($newStatus === 'sudah_bayar') {
                try {
                    DB::table('tb_transaksi')
                        ->where('id_transaksi', $orderId)
                        ->update([
                            'midtrans_order_id' => null,
                            'snap_token' => null
                        ]);

                    Log::info('✅ Cleared retry payment fields after successful payment', [
                        'order_id' => $orderId,
                        'order_code' => $order->kode_transaksi
                    ]);
                } catch (\Exception $e) {
                    Log::warning('⚠️ Failed to clear retry payment fields (non-critical): ' . $e->getMessage(), [
                        'order_id' => $orderId
                    ]);
                }
            }

            // ========== STEP 6: Reload from Database with FRESH query ==========
            // Reload fresh order from database (Eloquent will handle caching)
            $freshOrder = Order::find($orderId);

            // Additional verification - do a raw DB query to double-check
            $rawVerify = DB::table('tb_transaksi')
                ->where('id_transaksi', $orderId)
                ->select('id_transaksi', 'status_pembayaran', 'status_pengiriman')
                ->first();

            if ($rawVerify) {
                Log::info('✅ Final verification - Raw DB Query', [
                    'order_id' => $rawVerify->id_transaksi,
                    'status_pembayaran' => $rawVerify->status_pembayaran,
                    'status_pengiriman' => $rawVerify->status_pengiriman
                ]);
            }

            Log::info('✅ PaymentUpdateService::updatePaymentStatus COMPLETE', [
                'order_id' => $freshOrder->id_transaksi,
                'order_code' => $freshOrder->kode_transaksi,
                'final_status' => $freshOrder->status_pembayaran,
                'total_attempts' => $attempt
            ]);

            return [
                'success' => true,
                'message' => "Status pembayaran berhasil diupdate menjadi $newStatus",
                'data' => $freshOrder
            ];

        } catch (\Exception $e) {
            Log::error('❌ PaymentUpdateService Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Verify payment status dari database
     *
     * @param int $orderId
     * @return array
     */
    public static function verifyPaymentStatus($orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return [
                    'success' => false,
                    'is_paid' => false,
                    'status' => 'Order not found'
                ];
            }

            $isPaid = $order->status_pembayaran === 'sudah_bayar';

            Log::info('Payment status verified', [
                'order_id' => $orderId,
                'order_code' => $order->kode_transaksi,
                'status_pembayaran' => $order->status_pembayaran,
                'is_paid' => $isPaid
            ]);

            return [
                'success' => true,
                'is_paid' => $isPaid,
                'status_pembayaran' => $order->status_pembayaran,
                'status_pengiriman' => $order->status_pengiriman,
                'message' => $isPaid ? 'Sudah dibayar' : 'Belum dibayar'
            ];

        } catch (\Exception $e) {
            Log::error('Error verifying payment status: ' . $e->getMessage());

            return [
                'success' => false,
                'is_paid' => false,
                'status' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get payment status untuk display
     *
     * @param int $orderId
     * @return string
     */
    public static function getPaymentStatusDisplay($orderId)
    {
        $result = self::verifyPaymentStatus($orderId);

        if (!$result['success']) {
            return 'Unknown';
        }

        return match ($result['status_pembayaran']) {
            'sudah_bayar' => 'Sudah Dibayar ✅',
            'belum_bayar' => 'Belum Dibayar ⏳',
            'kadaluarsa' => 'Kadaluarsa ❌',
            default => 'Status: ' . $result['status_pembayaran']
        };
    }
}
