<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->command->info('Syncing products from integrasi database...');
            $productsIntegrasi = DB::connection('mysql_integrasi')->table('tb_produk')->get();
            
            foreach ($productsIntegrasi as $product) {
                // Parse and convert weight to kilograms if necessary
                $rawBerat = $product->berat_produk;
                $numericBerat = 0.0;
                if (!empty($rawBerat)) {
                    preg_match('/([0-9]+(?:\.[0-9]+)?)\s*([a-zA-Z]+)?/', $rawBerat, $matches);
                    if (isset($matches[1])) {
                        $val = floatval($matches[1]);
                        $unit = isset($matches[2]) ? strtolower($matches[2]) : 'kg';
                        if ($unit === 'gram' || $unit === 'gr' || $unit === 'g' || $unit === 'ml') {
                            $numericBerat = $val / 1000;
                        } else {
                            $numericBerat = $val;
                        }
                    }
                }

                DB::table('tb_produk')->updateOrInsert(
                    ['id_produk' => $product->id_produk],
                    [
                        'id_produk_integrasi' => $product->id_produk,
                        'kode_produk' => $product->kode_produk,
                        'nama_produk' => $product->nama_produk,
                        'deskripsi_produk' => $product->deskripsi_produk,
                        'harga_produk' => $product->harga_produk,
                        'harga_diskon' => $product->harga_diskon,
                        'persentase_diskon' => $product->persentase_diskon ?? null,
                        'tanggal_mulai_diskon' => $product->tanggal_mulai_diskon ?? null,
                        'tanggal_akhir_diskon' => $product->tanggal_akhir_diskon ?? null,
                        'is_diskon_active' => $product->is_diskon_active ?? 0,
                        'diskon_produk' => $product->diskon_produk ?? ($product->harga_diskon ? ($product->harga_produk - $product->harga_diskon) : 0),
                        'stok_produk' => $product->stok_produk ?? 100,
                        'berat_produk' => $numericBerat,
                        'foto_produk' => $product->foto_produk,
                        'status_produk' => $product->status_produk,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
            $this->command->info('✓ Synced ' . $productsIntegrasi->count() . ' products successfully.');
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Could not connect to mysql_integrasi or failed during insertion. Using fallback hardcoded products.');
            Log::warning('ProductSeeder error: ' . $e->getMessage());

            $fallbackProducts = [
                [
                    'id_produk' => 1,
                    'kode_produk' => 'PRD001',
                    'nama_produk' => 'Beras Premium 5kg',
                    'deskripsi_produk' => 'Beras premium kualitas terbaik, pulen dan wangi',
                    'harga_produk' => 75000,
                    'diskon_produk' => 5000,
                    'stok_produk' => 100,
                    'berat_produk' => 5.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Beras+Premium',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 2,
                    'kode_produk' => 'PRD002',
                    'nama_produk' => 'Minyak Goreng 2L',
                    'deskripsi_produk' => 'Minyak goreng kemasan 2 liter',
                    'harga_produk' => 35000,
                    'diskon_produk' => 3000,
                    'stok_produk' => 150,
                    'berat_produk' => 2.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Minyak+Goreng',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 3,
                    'kode_produk' => 'PRD003',
                    'nama_produk' => 'Gula Pasir 1kg',
                    'deskripsi_produk' => 'Gula pasir murni berkualitas',
                    'harga_produk' => 15000,
                    'diskon_produk' => 0,
                    'stok_produk' => 200,
                    'berat_produk' => 1.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Gula+Pasir',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 4,
                    'kode_produk' => 'PRD004',
                    'nama_produk' => 'Tepung Terigu 1kg',
                    'deskripsi_produk' => 'Tepung terigu serbaguna untuk berbagai masakan',
                    'harga_produk' => 12000,
                    'diskon_produk' => 1000,
                    'stok_produk' => 120,
                    'berat_produk' => 1.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Tepung+Terigu',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 5,
                    'kode_produk' => 'PRD005',
                    'nama_produk' => 'Kopi Bubuk 200g',
                    'deskripsi_produk' => 'Kopi bubuk premium dengan aroma khas',
                    'harga_produk' => 25000,
                    'diskon_produk' => 5000,
                    'stok_produk' => 80,
                    'berat_produk' => 0.2,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Kopi+Bubuk',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 6,
                    'kode_produk' => 'PRD006',
                    'nama_produk' => 'Susu UHT 1L',
                    'deskripsi_produk' => 'Susu UHT full cream kemasan 1 liter',
                    'harga_produk' => 18000,
                    'diskon_produk' => 2000,
                    'stok_produk' => 100,
                    'berat_produk' => 1.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Susu+UHT',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 7,
                    'kode_produk' => 'PRD007',
                    'nama_produk' => 'Telur Ayam 1kg',
                    'deskripsi_produk' => 'Telur ayam segar berkualitas',
                    'harga_produk' => 28000,
                    'diskon_produk' => 0,
                    'stok_produk' => 90,
                    'berat_produk' => 1.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Telur+Ayam',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 8,
                    'kode_produk' => 'PRD008',
                    'nama_produk' => 'Daging Ayam 1kg',
                    'deskripsi_produk' => 'Daging ayam segar tanpa tulang',
                    'harga_produk' => 45000,
                    'diskon_produk' => 5000,
                    'stok_produk' => 50,
                    'berat_produk' => 1.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Daging+Ayam',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 9,
                    'kode_produk' => 'PRD009',
                    'nama_produk' => 'Sabun Cuci Piring 800ml',
                    'deskripsi_produk' => 'Sabun cuci piring anti bakteri',
                    'harga_produk' => 12000,
                    'diskon_produk' => 2000,
                    'stok_produk' => 150,
                    'berat_produk' => 0.8,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Sabun+Cuci',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 10,
                    'kode_produk' => 'PRD010',
                    'nama_produk' => 'Detergen 1kg',
                    'deskripsi_produk' => 'Detergen bubuk wangi segar',
                    'harga_produk' => 18000,
                    'diskon_produk' => 0,
                    'stok_produk' => 120,
                    'berat_produk' => 1.0,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Detergen',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 11,
                    'kode_produk' => 'PRD011',
                    'nama_produk' => 'Shampoo 340ml',
                    'deskripsi_produk' => 'Shampoo anti ketombe',
                    'harga_produk' => 22000,
                    'diskon_produk' => 3000,
                    'stok_produk' => 80,
                    'berat_produk' => 0.34,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Shampoo',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_produk' => 12,
                    'kode_produk' => 'PRD012',
                    'nama_produk' => 'Pasta Gigi 150g',
                    'deskripsi_produk' => 'Pasta gigi untuk gigi putih',
                    'harga_produk' => 15000,
                    'diskon_produk' => 2000,
                    'stok_produk' => 100,
                    'berat_produk' => 0.15,
                    'foto_produk' => 'https://via.placeholder.com/400x400?text=Pasta+Gigi',
                    'status_produk' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($fallbackProducts as $product) {
                DB::table('tb_produk')->updateOrInsert(
                    ['id_produk' => $product['id_produk']],
                    [
                        'kode_produk' => $product['kode_produk'],
                        'nama_produk' => $product['nama_produk'],
                        'deskripsi_produk' => $product['deskripsi_produk'],
                        'harga_produk' => $product['harga_produk'],
                        'diskon_produk' => $product['diskon_produk'],
                        'stok_produk' => $product['stok_produk'],
                        'berat_produk' => $product['berat_produk'],
                        'foto_produk' => $product['foto_produk'],
                        'status_produk' => $product['status_produk'],
                        'created_at' => $product['created_at'],
                        'updated_at' => $product['updated_at'],
                    ]
                );
            }
        }
    }
}
