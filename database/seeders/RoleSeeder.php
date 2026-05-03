<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'owner',
                'display_name' => 'Owner',
                'description' => 'Pemilik sistem yang memiliki akses penuh ke laporan dan analytics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrator yang mengelola produk, transaksi, dan akun staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'cs',
                'display_name' => 'Customer Service',
                'description' => 'Customer service yang menangani ticketing dan newsletter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'kurir',
                'display_name' => 'Kurir',
                'description' => 'Kurir yang menangani pengiriman barang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'pelanggan',
                'display_name' => 'Pelanggan',
                'description' => 'Pelanggan yang dapat berbelanja dan memberikan review',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}

