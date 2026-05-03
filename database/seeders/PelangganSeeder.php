<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelangganData = [
            [
                'nama' => 'Pelanggan Demo',
                'email' => 'pelanggan@crm.com',
                'password' => Hash::make('password'),
                'phone' => '081234567894',
                'alamat' => 'Jl. Contoh No. 123, Jakarta Selatan',
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '081234567895',
                'alamat' => 'Jl. Merdeka No. 45, Bandung',
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($pelangganData as $data) {
                // Check if user already exists
                $existingUser = User::where('email', $data['email'])->first();

                if (!$existingUser) {
                    // Create user account
                    $user = User::create([
                        'id_role' => 5, // Pelanggan
                        'email' => $data['email'],
                        'password' => $data['password'],
                    ]);

                    // Create pelanggan data
                    Pelanggan::create([
                        'id_user' => $user->id_user,
                        'nama_pelanggan' => $data['nama'],
                        'no_tlp_pelanggan' => $data['phone'],
                        'alamat' => $data['alamat'],
                        'status_pelanggan' => 'aktif',
                    ]);

                    echo "✓ Created pelanggan: {$data['nama']} ({$data['email']})\n";
                } else {
                    echo "⊘ Pelanggan already exists: {$data['email']}\n";
                }
            }

            DB::commit();
            echo "\n✓ Pelanggan seeder completed successfully!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
}

