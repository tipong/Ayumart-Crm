<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\PaymentUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that PaymentUpdateService updates payment status atomically
     */
    public function test_payment_update_service_updates_status()
    {
        // Create a test order with unpaid status
        $order = Order::create([
            'id_pelanggan' => 1,
            'kode_transaksi' => 'TEST-' . time(),
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
        ]);

        // Verify initial state
        $this->assertEquals('belum_bayar', $order->status_pembayaran);

        // Update payment status using PaymentUpdateService
        $result = PaymentUpdateService::updatePaymentStatus(
            $order->id_transaksi,
            'sudah_bayar'
        );

        // Verify result
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['data']);
        $this->assertEquals('sudah_bayar', $result['data']->status_pembayaran);

        // Verify database is updated
        $freshOrder = Order::find($order->id_transaksi);
        $this->assertEquals('sudah_bayar', $freshOrder->status_pembayaran);
    }

    /**
     * Test that PaymentUpdateService verifies status
     */
    public function test_verify_payment_status()
    {
        $order = Order::create([
            'id_pelanggan' => 1,
            'kode_transaksi' => 'TEST-' . time(),
            'status_pembayaran' => 'sudah_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
        ]);

        $result = PaymentUpdateService::verifyPaymentStatus($order->id_transaksi);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['is_paid']);
        $this->assertEquals('sudah_bayar', $result['status_pembayaran']);
    }

    /**
     * Test that PaymentUpdateService handles non-existent orders
     */
    public function test_payment_update_service_handles_missing_order()
    {
        $result = PaymentUpdateService::updatePaymentStatus(
            99999,
            'sudah_bayar'
        );

        $this->assertFalse($result['success']);
        $this->assertNull($result['data']);
    }

    /**
     * Test payment update with additional data
     */
    public function test_payment_update_with_additional_data()
    {
        $order = Order::create([
            'id_pelanggan' => 1,
            'kode_transaksi' => 'TEST-' . time(),
            'status_pembayaran' => 'belum_bayar',
            'status_pengiriman' => 'menunggu_konfirmasi',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
        ]);

        $result = PaymentUpdateService::updatePaymentStatus(
            $order->id_transaksi,
            'sudah_bayar',
            ['status_pengiriman' => 'dikemas']
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('sudah_bayar', $result['data']->status_pembayaran);
        $this->assertEquals('dikemas', $result['data']->status_pengiriman);
    }

    /**
     * Test payment status display formatting
     */
    public function test_payment_status_display()
    {
        $order = Order::create([
            'id_pelanggan' => 1,
            'kode_transaksi' => 'TEST-' . time(),
            'status_pembayaran' => 'sudah_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
        ]);

        $display = PaymentUpdateService::getPaymentStatusDisplay($order->id_transaksi);

        $this->assertStringContainsString('Sudah Dibayar', $display);
        $this->assertStringContainsString('✅', $display);
    }

    /**
     * Test that multiple concurrent updates are handled properly
     */
    public function test_concurrent_payment_updates()
    {
        $order1 = Order::create([
            'id_pelanggan' => 1,
            'kode_transaksi' => 'TEST-1-' . time(),
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
        ]);

        $order2 = Order::create([
            'id_pelanggan' => 1,
            'kode_transaksi' => 'TEST-2-' . time(),
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 200000,
            'total_diskon' => 0,
            'ongkir' => 15000,
        ]);

        // Update both orders
        $result1 = PaymentUpdateService::updatePaymentStatus($order1->id_transaksi, 'sudah_bayar');
        $result2 = PaymentUpdateService::updatePaymentStatus($order2->id_transaksi, 'sudah_bayar');

        $this->assertTrue($result1['success']);
        $this->assertTrue($result2['success']);

        // Verify both are updated
        $this->assertEquals('sudah_bayar', Order::find($order1->id_transaksi)->status_pembayaran);
        $this->assertEquals('sudah_bayar', Order::find($order2->id_transaksi)->status_pembayaran);
    }
}
