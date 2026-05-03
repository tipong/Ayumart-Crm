<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Staff;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates dummy staff data for each role (Owner, Admin, CS, Kurir)
     */
    public function run(): void
    {
        // Data staff dummy
        $staffData = [
            [
                // Owner
                'name' => 'Budi Santoso',
                'email' => 'owner@crm.com',
                'phone' => '081234567890',
                'role_id' => 1,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Lulusan S2 Manajemen Bisnis dari Universitas Indonesia. Berpengalaman 10 tahun di bidang retail dan e-commerce. Founder dan Owner dari CRM System.',
            ],
            [
                // Admin 1
                'name' => 'Siti Nurhaliza',
                'email' => 'admin@crm.com',
                'phone' => '081234567891',
                'role_id' => 2,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Lulusan S1 Sistem Informasi. Berpengalaman 5 tahun mengelola sistem CRM dan manajemen inventory. Ahli dalam manajemen produk dan diskon.',
            ],
            [
                // Admin 2
                'name' => 'Andi Wijaya',
                'email' => 'admin2@crm.com',
                'phone' => '081234567894',
                'role_id' => 2,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Lulusan S1 Manajemen. Berpengalaman 3 tahun dalam administrasi bisnis dan manajemen staff. Bertanggung jawab dalam pengelolaan transaksi dan laporan.',
            ],
            [
                // Customer Service 1
                'name' => 'Dewi Lestari',
                'email' => 'cs@crm.com',
                'phone' => '081234567892',
                'role_id' => 3,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Lulusan D3 Komunikasi. Berpengalaman 4 tahun di bidang customer service. Ramah, responsif, dan ahli dalam menangani komplain pelanggan serta newsletter.',
            ],
            [
                // Customer Service 2
                'name' => 'Rina Wijayanti',
                'email' => 'cs2@crm.com',
                'phone' => '081234567895',
                'role_id' => 3,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Lulusan SMA dengan pelatihan Customer Service Excellence. Berpengalaman 2 tahun dalam ticketing system dan handling customer inquiries.',
            ],
            [
                // Kurir 1
                'name' => 'Ahmad Yusuf',
                'email' => 'kurir@crm.com',
                'phone' => '081234567893',
                'role_id' => 4,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Lulusan SMK Logistik. Berpengalaman 3 tahun sebagai kurir. Memiliki SIM C dan kendaraan pribadi. Familiar dengan area Jakarta dan sekitarnya.',
            ],
            [
                // Kurir 2
                'name' => 'Rudi Hermawan',
                'email' => 'kurir2@crm.com',
                'phone' => '081234567896',
                'role_id' => 4,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Lulusan SMA. Berpengalaman 2 tahun sebagai kurir ekspedisi. Cepat, tepat waktu, dan bertanggung jawab dalam pengiriman barang.',
            ],
            [
                // Kurir 3
                'name' => 'Doni Prasetyo',
                'email' => 'kurir3@crm.com',
                'phone' => '081234567897',
                'role_id' => 4,
                'password' => Hash::make('password123'),
                'profil_staff' => 'Berpengalaman 1 tahun sebagai kurir. Memiliki motor dan SIM C. Menguasai rute-rute di wilayah Bogor dan sekitarnya.',
            ],
        ];

        // Mapping role_id ke posisi_staff
        $posisiMap = [
            1 => 'Owner',
            2 => 'Admin',
            3 => 'Customer Service',
            4 => 'Kurir',
        ];

        // Insert data dengan transaction
        DB::beginTransaction();

        try {
            foreach ($staffData as $data) {
                // Cek apakah email sudah ada
                $existingUser = User::where('email', $data['email'])->first();

                if (!$existingUser) {
                    // Create user account
                    $user = User::create([
                        'id_role' => $data['role_id'],
                        'email' => $data['email'],
                        'password' => $data['password'],
                    ]);

                    // Create staff biodata
                    Staff::create([
                        'id_user' => $user->id_user,
                        'nama_staff' => $data['name'],
                        'email_staff' => $data['email'],
                        'posisi_staff' => $posisiMap[$data['role_id']],
                        'profil_staff' => $data['profil_staff'],
                        'no_tlp_staff' => $data['phone'],
                        'status_akun' => 'aktif',
                    ]);

                    echo "✓ Created staff: {$data['name']} ({$data['email']})\n";
                } else {
                    echo "⊘ Staff already exists: {$data['email']}\n";
                }
            }

            DB::commit();
            echo "\n✓ Staff seeder completed successfully!\n";
            echo "═══════════════════════════════════════════════════════\n";
            echo "Login credentials (email / password):\n";
            echo "─────────────────────────────────────────────────────\n";
            echo "Owner       : owner@crm.com / password123\n";
            echo "Admin 1     : admin@crm.com / password123\n";
            echo "Admin 2     : admin2@crm.com / password123\n";
            echo "CS 1        : cs@crm.com / password123\n";
            echo "CS 2        : cs2@crm.com / password123\n";
            echo "Kurir 1     : kurir@crm.com / password123\n";
            echo "Kurir 2     : kurir2@crm.com / password123\n";
            echo "Kurir 3     : kurir3@crm.com / password123\n";
            echo "═══════════════════════════════════════════════════════\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
}

