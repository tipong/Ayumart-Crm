<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Staff;
use App\Models\Pelanggan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            [
                'email' => 'owner@crm.com',
                'password' => Hash::make('password123'),
                'id_role' => 1, // owner
                'name' => 'Owner Demo',
                'phone' => '081234567890',
                'profile' => 'Owner account for demonstration purposes.',
            ],
            [
                'email' => 'admin@crm.com',
                'password' => Hash::make('password123'),
                'id_role' => 2, // admin
                'name' => 'Admin Demo',
                'phone' => '081234567891',
                'profile' => 'Admin account for demonstration purposes.',
            ],
            [
                'email' => 'cs@crm.com',
                'password' => Hash::make('password123'),
                'id_role' => 3, // cs
                'name' => 'CS Demo',
                'phone' => '081234567892',
                'profile' => 'Customer Service account for demonstration purposes.',
            ],
            [
                'email' => 'kurir@crm.com',
                'password' => Hash::make('password123'),
                'id_role' => 4, // kurir
                'name' => 'Kurir Demo',
                'phone' => '081234567893',
                'profile' => 'Courier account for demonstration purposes.',
            ],
            [
                'email' => 'pelanggan@crm.com',
                'password' => Hash::make('password123'),
                'id_role' => 5, // pelanggan
                'name' => 'Pelanggan Demo',
                'phone' => '081234567894',
                'alamat' => 'Jl. Contoh No. 123, Jakarta Selatan',
            ],
        ];

        $posisiMap = [
            1 => 'Owner',
            2 => 'Admin',
            3 => 'Customer Service',
            4 => 'Kurir',
        ];

        DB::beginTransaction();

        try {
            foreach ($usersData as $data) {
                // Check if user already exists
                $user = User::where('email', $data['email'])->first();

                if (!$user) {
                    // Create user
                    $user = User::create([
                        'id_role' => $data['id_role'],
                        'email' => $data['email'],
                        'password' => $data['password'],
                    ]);

                    echo "✓ Created user: {$data['email']} with role ID: {$data['id_role']}\n";
                }

                // If staff role (1-4), check and create staff profile
                if ($data['id_role'] >= 1 && $data['id_role'] <= 4) {
                    $staffExists = Staff::where('id_user', $user->id_user)->exists();
                    if (!$staffExists) {
                        Staff::create([
                            'id_user' => $user->id_user,
                            'nama_staff' => $data['name'],
                            'email_staff' => $data['email'],
                            'posisi_staff' => $posisiMap[$data['id_role']],
                            'profil_staff' => $data['profile'] ?? null,
                            'no_tlp_staff' => $data['phone'] ?? null,
                            'status_akun' => 'aktif',
                        ]);
                        echo "  └─ Profile Staff created for {$data['name']}\n";
                    }
                } 
                // If pelanggan role (5), check and create pelanggan profile
                elseif ($data['id_role'] == 5) {
                    $pelangganExists = Pelanggan::where('id_user', $user->id_user)->exists();
                    if (!$pelangganExists) {
                        Pelanggan::create([
                            'id_user' => $user->id_user,
                            'nama_pelanggan' => $data['name'],
                            'no_tlp_pelanggan' => $data['phone'] ?? null,
                            'alamat' => $data['alamat'] ?? null,
                            'status_pelanggan' => 'aktif',
                        ]);
                        echo "  └─ Profile Pelanggan created for {$data['name']}\n";
                    }
                }
            }

            DB::commit();
            echo "\n✓ User seeder completed successfully!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "✗ Error in UserSeeder: " . $e->getMessage() . "\n";
        }
    }
}
