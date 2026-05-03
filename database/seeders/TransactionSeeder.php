<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Models\Product;
use App\Models\Cabang;
use App\Models\Membership;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generate dummy transactions for 2024 and 2025
     */
    public function run(): void
    {
        // Get all customers (role_id = 5) that have a pelanggan record
        $customers = User::where('role_id', 5)
            ->whereHas('pelanggan')
            ->with('pelanggan')
            ->get();

        if ($customers->isEmpty()) {
            $this->command->error('No customers with pelanggan records found! Please run UserSeeder and PelangganSeeder first.');
            return;
        }

        // Get all products
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->error('No products found! Please run ProductSeeder first.');
            return;
        }

        // Get all branches
        $branches = Cabang::all();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found! Please run CabangSeeder first.');
            return;
        }

        $this->command->info('🚀 Starting transaction seeder...');

        // Generate transactions for 2024
        $this->command->info('📅 Generating transactions for 2024...');
        $this->generateTransactionsForYear(2024, $customers, $products, $branches, 300);

        // Generate transactions for 2025
        $this->command->info('📅 Generating transactions for 2025...');
        $this->generateTransactionsForYear(2025, $customers, $products, $branches, 200);

        $this->command->info('✅ Transaction seeder completed successfully!');
    }

    /**
     * Generate transactions for a specific year
     */
    private function generateTransactionsForYear($year, $customers, $products, $branches, $count)
    {
        $metodeOptions = ['tunai', 'transfer', 'debit', 'qris'];
        $pengirimanOptions = ['kurir', 'ambil_sendiri'];
        $statusPembayaranOptions = ['sudah_bayar', 'belum_bayar'];

        for ($i = 0; $i < $count; $i++) {
            // Random date in the year
            $randomDate = Carbon::create($year, rand(1, 12), rand(1, 28), rand(8, 20), rand(0, 59));

            // Pick random customer
            $customer = $customers->random();
            $membership = $customer->membership;

            // Pick random branch
            $branch = $branches->random();

            // Random shipping method
            $metodePengiriman = $pengirimanOptions[array_rand($pengirimanOptions)];

            // Random payment method
            $metodePembayaran = $metodeOptions[array_rand($metodeOptions)];

            // Random payment status (80% paid, 20% unpaid)
            $statusPembayaran = rand(1, 100) <= 80 ? 'sudah_bayar' : 'belum_bayar';

            // Random number of products (1-5)
            $numProducts = rand(1, 5);
            $selectedProducts = $products->random(min($numProducts, $products->count()));

            // Calculate totals
            $subtotal = 0;
            $productDetails = [];

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $price = $product->harga_produk;
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                $productDetails[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal
                ];
            }

            // Apply membership discount if applicable
            $discount = 0;
            $discountPercentage = 0;
            if ($membership && $membership->is_active && $membership->isValid()) {
                $discountPercentage = $membership->discount_percentage;
                $discount = ($subtotal * $discountPercentage) / 100;
            }

            // Shipping cost (0 for pickup, 10000-50000 for delivery)
            $ongkir = $metodePengiriman === 'ambil_sendiri' ? 0 : rand(10, 50) * 1000;

            // Membership fee (only for first transaction and no existing membership)
            $biayaMember = 0;
            $existingOrders = Order::where('id_pelanggan', $customer->pelanggan?->id_pelanggan ?? null)->count();
            if ($existingOrders == 0 && !$membership) {
                $biayaMember = 50000; // 50k membership fee
            }

            // Total
            $total = $subtotal - $discount + $ongkir + $biayaMember;

            // Calculate points earned
            $pointsEarned = floor($total / 20000); // 1 point per 20k

            // Generate unique transaction code
            $kodeTransaksi = 'TRX-' . $year . str_pad($i + 1, 6, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'id_pelanggan' => $customer->pelanggan?->id_pelanggan ?? null,
                'id_cabang' => $branch->id_cabang,
                'kode_transaksi' => $kodeTransaksi,
                'tgl_transaksi' => $randomDate,
                'total_harga' => $total,
                'metode_pembayaran' => $metodePembayaran,
                'metode_pengiriman' => $metodePengiriman,
                'status_pembayaran' => $statusPembayaran,
                'status_pengiriman' => $statusPembayaran === 'sudah_bayar' ? 'dikemas' : 'pending',
                'catatan' => $this->getRandomNote(),
                'total_diskon' => $discount,
                'ongkir' => $ongkir,
                'biaya_membership' => $biayaMember,
                'alamat_pengiriman' => $metodePengiriman === 'kurir' ? 'Alamat pengiriman pelanggan' : null,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            // Create order details
            foreach ($productDetails as $detail) {
                TransactionDetail::create([
                    'id_transaksi' => $order->id_transaksi,
                    'id_produk' => $detail['product']->id_produk,
                    'qty' => $detail['quantity'],
                    'harga_item' => $detail['price'],
                    'subtotal' => $detail['subtotal'],
                    'diskon_item' => 0,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
            }

            // Add points to membership if paid and membership exists
            if ($statusPembayaran === 'sudah_bayar' && $membership && $pointsEarned > 0) {
                $membership->addPoints($pointsEarned);
            }

            // Create membership if this is first transaction and membership fee paid
            if ($biayaMember > 0 && $statusPembayaran === 'sudah_bayar') {
                Membership::create([
                    'user_id' => $customer->id,
                    'tier' => 'bronze',
                    'points' => $pointsEarned,
                    'discount_percentage' => 5,
                    'valid_from' => $randomDate,
                    'valid_until' => $randomDate->copy()->addYear(),
                    'is_active' => true,
                ]);
            }

            if (($i + 1) % 50 == 0) {
                $this->command->info("   Generated " . ($i + 1) . "/$count transactions for $year");
            }
        }

        $this->command->info("   ✓ Completed $count transactions for $year");
    }

    /**
     * Get random order note
     */
    private function getRandomNote()
    {
        $notes = [
            null,
            'Tolong kirim secepatnya',
            'Hubungi saya jika ada masalah',
            'Kirim ke alamat kantor',
            'Jangan kirim saat hujan',
            'Packing yang rapi ya',
            'Terima kasih',
            'Mohon cek kualitas produk sebelum kirim',
        ];

        return $notes[array_rand($notes)];
    }
}
