<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Membership;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customer users (role ID 5)
        $customers = User::where('id_role', 5)->get();

        foreach ($customers as $customer) {
            // Check if membership already exists for this user
            $existing = Membership::where('user_id', $customer->id_user)->first();

            if (!$existing) {
                // Let's give some random tier and points
                $points = rand(50, 450);
                $tier = Membership::calculateTier($points);
                $discount = Membership::getDiscountForTier($tier);

                Membership::create([
                    'user_id' => $customer->id_user,
                    'tier' => $tier,
                    'points' => $points,
                    'discount_percentage' => $discount,
                    'valid_from' => now()->subMonths(3),
                    'valid_until' => now()->addYear(),
                    'is_active' => true,
                ]);

                echo "✓ Created membership for {$customer->email}: Tier {$tier}, Points {$points}\n";
            }
        }
    }
}
