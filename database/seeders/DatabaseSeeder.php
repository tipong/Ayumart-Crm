<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            StaffSeeder::class,
            UserSeeder::class,
            MembershipSeeder::class,
            JenisSeeder::class,
            ProductSeeder::class,
            PelangganSeeder::class,
            CabangSeeder::class,
            TransactionSeeder::class, // Generate dummy transactions for 2024 and 2025
        ]);
    }
}
