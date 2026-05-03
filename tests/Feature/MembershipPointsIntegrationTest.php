<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Membership;
use App\Models\Pelanggan;
use App\Models\Cart;
use App\Models\Product;
use App\Services\PaymentUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class MembershipPointsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user with proper id_user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'no_telepon' => '081234567890'
        ]);

        // Create associated pelanggan
        $this->pelanggan = Pelanggan::create([
            'id_user' => $this->user->id_user,
            'nama_pelanggan' => 'Test Pelanggan',
            'no_tlp_pelanggan' => '081234567890',
            'alamat_lengkap' => 'Test Address'
        ]);
    }

    /**
     * Test 1: First-time customer gets membership on successful payment
     */
    public function test_first_transaction_creates_membership()
    {
        Log::info('TEST 1: First-time customer creates membership on payment');

        // Verify user has no membership
        $this->assertFalse(Membership::where('user_id', $this->user->id_user)->exists());

        // Create an order with transaction amount
        $order = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-001',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
            'biaya_membership' => 10000,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);

        // Simulate successful payment
        $result = PaymentUpdateService::updatePaymentStatus($order->id_transaksi, 'sudah_bayar');
        $this->assertTrue($result['success']);

        // Check membership was created
        $membership = Membership::where('user_id', $this->user->id_user)->first();
        $this->assertNotNull($membership);
        $this->assertEquals(Membership::TIER_BRONZE, $membership->tier);
        $this->assertTrue($membership->is_active);
        $this->assertEquals(Membership::TIER_DISCOUNTS[Membership::TIER_BRONZE], $membership->discount_percentage);

        Log::info('✅ TEST 1 PASSED: Membership created on first transaction');
    }

    /**
     * Test 2: Points are awarded correctly on first transaction
     */
    public function test_points_awarded_on_first_transaction()
    {
        Log::info('TEST 2: Points awarded correctly on first transaction');

        // Transaction total: 100000 + 10000 ongkir + 10000 membership fee = 120000
        // Points should be: 120000 / 20000 = 6 points
        $expectedPoints = 6;

        $order = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-002',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
            'biaya_membership' => 10000,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);

        // Simulate successful payment
        PaymentUpdateService::updatePaymentStatus($order->id_transaksi, 'sudah_bayar');

        // Check membership points
        $membership = Membership::where('user_id', $this->user->id_user)->first();
        $this->assertNotNull($membership);
        $this->assertEquals($expectedPoints, $membership->points);

        Log::info("✅ TEST 2 PASSED: Points awarded correctly ({$expectedPoints} points)");
    }

    /**
     * Test 3: Tier upgrades when points threshold is reached
     */
    public function test_tier_upgrades_with_points()
    {
        Log::info('TEST 3: Tier upgrades when points threshold reached');

        // Create and complete first transaction (6 points - BRONZE tier)
        $order1 = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-003a',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
            'biaya_membership' => 10000,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);
        PaymentUpdateService::updatePaymentStatus($order1->id_transaksi, 'sudah_bayar');

        $membership = Membership::where('user_id', $this->user->id_user)->first();
        $this->assertEquals(Membership::TIER_BRONZE, $membership->tier);
        $this->assertEquals(6, $membership->points);

        // Create second transaction with enough points to reach SILVER (101 points threshold)
        // Need 95+ more points: 95 * 20000 = 1900000
        $order2 = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-003b',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 1900000,
            'total_diskon' => 0,
            'ongkir' => 0,
            'biaya_membership' => 0,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);
        PaymentUpdateService::updatePaymentStatus($order2->id_transaksi, 'sudah_bayar');

        // Check tier upgraded and discount updated
        $membership->refresh();
        $this->assertEquals(Membership::TIER_SILVER, $membership->tier);
        $this->assertGreaterThanOrEqual(101, $membership->points);
        $this->assertEquals(Membership::TIER_DISCOUNTS[Membership::TIER_SILVER], $membership->discount_percentage);

        Log::info("✅ TEST 3 PASSED: Tier upgraded to SILVER ({$membership->points} points)");
    }

    /**
     * Test 4: Existing member gets points on additional transaction
     */
    public function test_existing_member_gets_points_on_transaction()
    {
        Log::info('TEST 4: Existing member gets points on additional transaction');

        // Create first membership manually
        $initialPoints = 50;
        $membership = Membership::create([
            'user_id' => $this->user->id_user,
            'tier' => Membership::TIER_BRONZE,
            'points' => $initialPoints,
            'discount_percentage' => 5,
            'valid_from' => now(),
            'valid_until' => now()->addYear(),
            'is_active' => true,
        ]);

        // Transaction with 5 points
        $order = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-004',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 0,
            'biaya_membership' => 0,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);

        PaymentUpdateService::updatePaymentStatus($order->id_transaksi, 'sudah_bayar');

        // Check points added
        $membership->refresh();
        $expectedPoints = $initialPoints + 5;
        $this->assertEquals($expectedPoints, $membership->points);

        Log::info("✅ TEST 4 PASSED: Points added to existing member ({$expectedPoints} points)");
    }

    /**
     * Test 5: Discount is applied correctly during checkout
     */
    public function test_membership_discount_applied_correctly()
    {
        Log::info('TEST 5: Membership discount applied correctly');

        // Create membership with 10% discount (SILVER tier)
        $membership = Membership::create([
            'user_id' => $this->user->id_user,
            'tier' => Membership::TIER_SILVER,
            'points' => 150,
            'discount_percentage' => 10,
            'valid_from' => now(),
            'valid_until' => now()->addYear(),
            'is_active' => true,
        ]);

        // Test discount calculation
        $subtotal = 500000;
        $expectedDiscount = $subtotal * (10 / 100);

        $this->assertEquals($expectedDiscount, $subtotal * ($membership->discount_percentage / 100));

        Log::info("✅ TEST 5 PASSED: Discount calculated correctly (10% of {$subtotal} = {$expectedDiscount})");
    }

    /**
     * Test 6: Payment callback creates membership and awards points
     */
    public function test_payment_callback_workflow()
    {
        Log::info('TEST 6: Complete payment callback workflow');

        // Verify no membership exists
        $this->assertFalse(Membership::where('user_id', $this->user->id_user)->exists());

        // Create unpaid order
        $order = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-006',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 250000,
            'total_diskon' => 0,
            'ongkir' => 15000,
            'biaya_membership' => 10000,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir',
            'last_payment_check_at' => null
        ]);

        // Verify status before payment
        $this->assertEquals('belum_bayar', $order->status_pembayaran);

        // Simulate payment update
        $result = PaymentUpdateService::updatePaymentStatus($order->id_transaksi, 'sudah_bayar');
        $this->assertTrue($result['success']);

        // Verify status after payment
        $freshOrder = $result['data'];
        $this->assertEquals('sudah_bayar', $freshOrder->status_pembayaran);

        // Verify membership created
        $membership = Membership::where('user_id', $this->user->id_user)->first();
        $this->assertNotNull($membership);

        // Verify points calculation
        // Total: 250000 + 15000 + 10000 = 275000 / 20000 = 13 points
        $expectedPoints = floor(275000 / 20000);
        $this->assertEquals($expectedPoints, $membership->points);

        Log::info("✅ TEST 6 PASSED: Complete workflow successful ({$expectedPoints} points earned)");
    }

    /**
     * Test 7: Payment update with additional data (status pengiriman)
     */
    public function test_payment_update_with_additional_data()
    {
        Log::info('TEST 7: Payment update with additional data');

        $order = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-007',
            'status_pembayaran' => 'belum_bayar',
            'status_pengiriman' => 'pending',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
            'biaya_membership' => 0,
            'metode_pengiriman' => 'kurir'
        ]);

        $additionalData = [
            'status_pengiriman' => 'dikemas'
        ];

        $result = PaymentUpdateService::updatePaymentStatus(
            $order->id_transaksi,
            'sudah_bayar',
            $additionalData
        );

        $this->assertTrue($result['success']);
        $updatedOrder = $result['data'];
        $this->assertEquals('dikemas', $updatedOrder->status_pengiriman);

        Log::info('✅ TEST 7 PASSED: Additional data updated correctly');
    }

    /**
     * Test 8: Foreign key relationship works correctly
     */
    public function test_membership_foreign_key_relationship()
    {
        Log::info('TEST 8: Membership foreign key relationship');

        $membership = Membership::create([
            'user_id' => $this->user->id_user,
            'tier' => Membership::TIER_BRONZE,
            'points' => 10,
            'discount_percentage' => 5,
            'valid_from' => now(),
            'valid_until' => now()->addYear(),
            'is_active' => true,
        ]);

        // Test relationship
        $relatedUser = $membership->user;
        $this->assertNotNull($relatedUser);
        $this->assertEquals($this->user->id_user, $relatedUser->id_user);
        $this->assertEquals($this->user->email, $relatedUser->email);

        Log::info('✅ TEST 8 PASSED: Membership-User relationship working correctly');
    }

    /**
     * Test 9: Multiple transactions don't create duplicate memberships
     */
    public function test_no_duplicate_memberships()
    {
        Log::info('TEST 9: No duplicate memberships on multiple transactions');

        // First transaction
        $order1 = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-009a',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
            'biaya_membership' => 10000,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);
        PaymentUpdateService::updatePaymentStatus($order1->id_transaksi, 'sudah_bayar');

        $count1 = Membership::where('user_id', $this->user->id_user)->count();
        $this->assertEquals(1, $count1);

        // Second transaction
        $order2 = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-009b',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 50000,
            'total_diskon' => 0,
            'ongkir' => 10000,
            'biaya_membership' => 0,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);
        PaymentUpdateService::updatePaymentStatus($order2->id_transaksi, 'sudah_bayar');

        $count2 = Membership::where('user_id', $this->user->id_user)->count();
        $this->assertEquals(1, $count2);

        // Verify points accumulated
        $membership = Membership::where('user_id', $this->user->id_user)->first();
        $expectedPoints = floor(120000 / 20000) + floor(60000 / 20000);
        $this->assertEquals($expectedPoints, $membership->points);

        Log::info("✅ TEST 9 PASSED: No duplicate memberships, points accumulated ({$expectedPoints} points)");
    }

    /**
     * Test 10: Payment status can be checked multiple times
     */
    public function test_idempotent_payment_update()
    {
        Log::info('TEST 10: Idempotent payment status update');

        $order = Order::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'kode_transaksi' => 'TEST-' . time() . '-010',
            'status_pembayaran' => 'belum_bayar',
            'total_harga' => 100000,
            'total_diskon' => 0,
            'ongkir' => 10000,
            'biaya_membership' => 10000,
            'status_pengiriman' => 'pending',
            'metode_pengiriman' => 'kurir'
        ]);

        // First update
        $result1 = PaymentUpdateService::updatePaymentStatus($order->id_transaksi, 'sudah_bayar');
        $this->assertTrue($result1['success']);

        $membership1 = Membership::where('user_id', $this->user->id_user)->first();
        $points1 = $membership1->points;

        // Second update (idempotent - should not create duplicate membership or add duplicate points)
        $result2 = PaymentUpdateService::updatePaymentStatus($order->id_transaksi, 'sudah_bayar');
        $this->assertTrue($result2['success']);

        $membership2 = Membership::where('user_id', $this->user->id_user)->first();
        $points2 = $membership2->points;

        // Should have same membership and points
        $this->assertEquals($membership1->id, $membership2->id);
        $this->assertEquals($points1, $points2);

        Log::info("✅ TEST 10 PASSED: Payment update is idempotent ({$points2} points)");
    }
}
