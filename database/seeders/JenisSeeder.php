<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis = [
            [
                'nama_jenis' => 'Elektronik',
                'deskripsi_jenis' => 'Produk elektronik dan gadget',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis' => 'Fashion',
                'deskripsi_jenis' => 'Pakaian dan aksesoris',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis' => 'Makanan & Minuman',
                'deskripsi_jenis' => 'Makanan, minuman, dan snack',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis' => 'Kesehatan & Kecantikan',
                'deskripsi_jenis' => 'Produk kesehatan dan kecantikan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis' => 'Rumah Tangga',
                'deskripsi_jenis' => 'Peralatan dan perlengkapan rumah tangga',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tb_jenis')->insert($jenis);
    }
}

