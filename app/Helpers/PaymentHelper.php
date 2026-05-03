<?php

namespace App\Helpers;

use App\Models\Order;
use App\Services\PaymentUpdateService;
use Illuminate\Support\Facades\DB;

/**
 * Payment Helper - Utility functions untuk payment status
 */
class PaymentHelper
{
    /**
     * Get fresh payment status dari database
     * Disarankan untuk menampilkan status yang always up-to-date
     */
    public static function getPaymentStatus($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return [
                'status' => 'unknown',
                'is_paid' => false,
                'display' => 'Status tidak ditemukan'
            ];
        }

        // Always read fresh from database
        $fresh = DB::table('tb_transaksi')
            ->where('id_transaksi', $orderId)
            ->select('id_transaksi', 'status_pembayaran', 'status_pengiriman', 'kode_transaksi')
            ->first();

        if (!$fresh) {
            return [
                'status' => 'unknown',
                'is_paid' => false,
                'display' => 'Order tidak ditemukan di database'
            ];
        }

        $isPaid = $fresh->status_pembayaran === 'sudah_bayar';

        $display = match ($fresh->status_pembayaran) {
            'sudah_bayar' => '✅ Sudah Dibayar',
            'belum_bayar' => '⏳ Belum Dibayar',
            'kadaluarsa' => '❌ Kadaluarsa',
            default => 'Status: ' . $fresh->status_pembayaran
        };

        return [
            'order_id' => $fresh->id_transaksi,
            'order_code' => $fresh->kode_transaksi,
            'status' => $fresh->status_pembayaran,
            'status_pengiriman' => $fresh->status_pengiriman,
            'is_paid' => $isPaid,
            'display' => $display
        ];
    }

    /**
     * Check if order is paid, always from fresh DB query
     */
    public static function isPaid($orderId)
    {
        $result = self::getPaymentStatus($orderId);
        return $result['is_paid'];
    }

    /**
     * Get display text for payment status
     */
    public static function getStatusDisplay($orderId)
    {
        $result = self::getPaymentStatus($orderId);
        return $result['display'];
    }

    /**
     * Get CSS class untuk badge berdasarkan payment status
     */
    public static function getStatusBadgeClass($orderId)
    {
        $result = self::getPaymentStatus($orderId);

        return match ($result['status']) {
            'sudah_bayar' => 'badge bg-success',
            'belum_bayar' => 'badge bg-warning',
            'kadaluarsa' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Verify payment status dari Midtrans dan update database jika diperlukan
     * Useful untuk manual verification
     */
    public static function verifyAndUpdateFromMidtrans($orderId)
    {
        $result = PaymentUpdateService::verifyPaymentStatus($orderId);
        return $result;
    }
}
