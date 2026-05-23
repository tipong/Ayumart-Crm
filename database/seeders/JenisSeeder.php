<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->command->info('Syncing jenis (categories) from integrasi database...');
            $jenisIntegrasi = DB::connection('mysql_integrasi')->table('tb_jenis')->get();
            
            foreach ($jenisIntegrasi as $jenis) {
                DB::table('tb_jenis')->updateOrInsert(
                    ['id_jenis' => $jenis->id_jenis],
                    [
                        'nama_jenis' => $jenis->nama_jenis,
                        'deskripsi_jenis' => $jenis->deskripsi_jenis ?? '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
            $this->command->info('✓ Synced ' . $jenisIntegrasi->count() . ' jenis successfully.');
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Could not connect to mysql_integrasi for seeding. Using fallback hardcoded jenis.');
            Log::warning('JenisSeeder: mysql_integrasi connection failed, using fallback. Error: ' . $e->getMessage());

            $fallbackJenis = [
                [
                    'id_jenis' => 1,
                    'nama_jenis' => 'Makanan & Minuman',
                    'deskripsi_jenis' => 'Produk makanan dan minuman',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_jenis' => 2,
                    'nama_jenis' => 'Sembako',
                    'deskripsi_jenis' => 'Kebutuhan pokok sehari-hari',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_jenis' => 3,
                    'nama_jenis' => 'Peralatan Rumah Tangga',
                    'deskripsi_jenis' => 'Alat-alat keperluan rumah tangga',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_jenis' => 4,
                    'nama_jenis' => 'Elektronik',
                    'deskripsi_jenis' => 'Peralatan elektronik',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_jenis' => 5,
                    'nama_jenis' => 'Kesehatan & Kecantikan',
                    'deskripsi_jenis' => 'Produk kesehatan dan kecantikan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($fallbackJenis as $jenis) {
                DB::table('tb_jenis')->updateOrInsert(
                    ['id_jenis' => $jenis['id_jenis']],
                    [
                        'nama_jenis' => $jenis['nama_jenis'],
                        'deskripsi_jenis' => $jenis['deskripsi_jenis'],
                        'created_at' => $jenis['created_at'],
                        'updated_at' => $jenis['updated_at'],
                    ]
                );
            }
        }
    }
}
