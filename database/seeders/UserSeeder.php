<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'role_id' => 1, // owner
                'name' => 'Owner',
                'email' => 'owner@crm.com',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'address' => null,
                'city' => null,
                'postal_code' => null,
                'profile_photo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 2, // admin
                'name' => 'Admin',
                'email' => 'admin@crm.com',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'address' => null,
                'city' => null,
                'postal_code' => null,
                'profile_photo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 3, // cs
                'name' => 'Customer Service',
                'email' => 'cs@crm.com',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'address' => null,
                'city' => null,
                'postal_code' => null,
                'profile_photo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 4, // kurir
                'name' => 'Kurir',
                'email' => 'kurir@crm.com',
                'password' => Hash::make('password'),
                'phone' => '081234567893',
                'address' => null,
                'city' => null,
                'postal_code' => null,
                'profile_photo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 5, // pelanggan
                'name' => 'Pelanggan Demo',
                'email' => 'pelanggan@crm.com',
                'password' => Hash::make('password'),
                'phone' => '081234567894',
                'address' => 'Jl. Contoh No. 123',
                'city' => 'Jakarta',
                'postal_code' => '12345',
                'profile_photo' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}

