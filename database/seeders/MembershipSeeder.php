<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $memberships = [
            [
                'name' => 'Bronze',
                'description' => 'Member Bronze - Level awal',
                'discount_percentage' => 5.00,
                'min_purchase' => 0.00,
                'points_required' => 0,
                'benefits' => json_encode(['Diskon 5%', 'Priority support']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silver',
                'description' => 'Member Silver - Level menengah',
                'discount_percentage' => 10.00,
                'min_purchase' => 1000000.00,
                'points_required' => 100,
                'benefits' => json_encode(['Diskon 10%', 'Free shipping', 'Priority support']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold',
                'description' => 'Member Gold - Level premium',
                'discount_percentage' => 15.00,
                'min_purchase' => 5000000.00,
                'points_required' => 500,
                'benefits' => json_encode(['Diskon 15%', 'Free shipping', 'Exclusive products', 'VIP support']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('memberships')->insert($memberships);
    }
}

