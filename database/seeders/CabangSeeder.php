<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cabang;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'nama_cabang' => 'Ayu Mart - Jl. Cargo Kenanga',
                'kode_cabang' => 'AYM-001',
                'alamat' => 'Jl. Cargo Kenanga, Ubung Kaja',
                'kelurahan' => 'Ubung Kaja',
                'kecamatan' => 'Denpasar Utara',
                'kota' => 'Denpasar',
                'provinsi' => 'Bali',
                'kode_pos' => '80116',
                'latitude' => -8.625210,
                'longitude' => 115.192436,
                'google_maps_url' => 'https://maps.app.goo.gl/SBoaJytnFQLNRiGD9',
                'no_telepon' => '0361-4714083',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'is_active' => true,


            ],
            [
                'nama_cabang' => 'Ayu Mart - Jl. Gunung Guntur',
                'kode_cabang' => 'AYM-002',
                'alamat' => 'Jl. Gunung Guntur Gang Taman Sari 1 No.7B, Padang Sambian Kelod',
                'kelurahan' => 'Padangsambian Kelod',
                'kecamatan' => 'Denpasar Barat',
                'kota' => 'Denpasar',
                'provinsi' => 'Bali',
                'kode_pos' => '80117',
                'latitude' => -8.654794,
                'longitude' => 115.182708,
                'google_maps_url' => 'https://maps.app.goo.gl/NfwKmJPV7sVJHvMR7',
                'no_telepon' => '0813-5336-3083',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'is_active' => true,
            ],
            [
                'nama_cabang' => 'Ayu Mart - Jl. Kubu Gn',
                'kode_cabang' => 'AYM-003',
                'alamat' => 'Jl. Kubu Gn. No.103, Dalung',
                'kelurahan' => 'Dalung',
                'kecamatan' => 'Kuta Utara',
                'kota' => 'Badung',
                'provinsi' => 'Bali',
                'kode_pos' => '80361',
                'latitude' => -8.628597,
                'longitude' => 115.177540,
                'google_maps_url' => 'https://maps.app.goo.gl/6mQ2Bf2C6gg6VqqR6',
                'no_telepon' => '0361-9072066',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'is_active' => true,
            ],
            [
                'nama_cabang' => 'Ayu Mart - Jl. Kebo Iwa',
                'kode_cabang' => 'AYM-004',
                'alamat' => 'Jl. Kebo Iwa Selatan Padangsambian',
                'kelurahan' => 'Padangsambian Kaja',
                'kecamatan' => 'Denpasar Barat',
                'kota' => 'Denpasar',
                'provinsi' => 'Bali',
                'kode_pos' => '80116',
                'latitude' => -8.629766,
                'longitude' => 115.185527,
                'google_maps_url' => 'https://maps.app.goo.gl/HD2XHHPGBcLUgmjH7',
                'no_telepon' => '0361-4714437',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'is_active' => true,
            ],
            [
                'nama_cabang' => 'Ayu Mart - Jl. Karya Makmur',
                'kode_cabang' => 'AYM-005',
                'alamat' => 'Jl. Karya Makmur, No.03 Kargo, Ubung Kaja',
                'kelurahan' => 'Ubung Kaja',
                'kecamatan' => 'Denpasar Utara',
                'kota' => 'Denpasar',
                'provinsi' => 'Bali',
                'kode_pos' => '80116',
                'latitude' => -8.624507,
                'longitude' => 115.194751,
                'google_maps_url' => 'https://maps.app.goo.gl/tfTBoo6PvN1trgih6',
                'no_telepon' => '0361-9063893',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'is_active' => true,
            ],
            [
                'nama_cabang' => 'Ayu Mart - Jl. Gn. Andakasa',
                'kode_cabang' => 'AYM-006',
                'alamat' => 'Jl. Gn. Andakasa No.11, Padangsambian',
                'kelurahan' => 'Padangsambian',
                'kecamatan' => 'Denpasar Barat',
                'kota' => 'Denpasar',
                'provinsi' => 'Bali',
                'kode_pos' => '80118',
                'latitude' => -8.648285,
                'longitude' => 115.190004,
                'google_maps_url' => 'https://maps.app.goo.gl/e73bwFoqQt3GumXp6',
                'no_telepon' => '0859-5520-2267',
                'jam_buka' => '07:00:00',
                'jam_tutup' => '23:00:00',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Cabang::create($branch);
        }

        $this->command->info('Successfully seeded ' . count($branches) . ' branches!');
    }
}
