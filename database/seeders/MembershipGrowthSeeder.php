<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipGrowthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates dummy membership records spread across 12 months
     * to populate the "Pertumbuhan Member" chart in the admin dashboard.
     */
    public function run(): void
    {
        // Define 12 months of data with varying growth
        $membershipGrowthData = [
            // Month => number of memberships to create in that month
            'March 2025' => 5,
            'April 2025' => 8,
            'May 2025' => 12,
            'June 2025' => 15,
            'July 2025' => 10,
            'August 2025' => 18,
            'September 2025' => 14,
            'October 2025' => 20,
            'November 2025' => 16,
            'December 2025' => 22,
            'January 2026' => 25,
            'February 2026' => 19,
            'March 2026' => 28,
        ];

        $userId = 10; // Start from user_id 10 (adjust based on your users table)
        $tiers = ['bronze', 'silver', 'gold', 'platinum'];
        $discounts = [0, 5, 10, 15];

        echo "🌱 Seeding membership growth data...\n";

        foreach ($membershipGrowthData as $monthLabel => $count) {
            $date = Carbon::createFromFormat('F Y', $monthLabel);

            for ($i = 0; $i < $count; $i++) {
                $tier = $tiers[array_rand($tiers)];
                $tierIndex = array_search($tier, $tiers);
                $discount = $discounts[$tierIndex];

                try {
                    DB::table('memberships')->insert([
                        'user_id' => $userId,
                        'tier' => $tier,
                        'points' => rand(0, 500),
                        'discount_percentage' => $discount,
                        'valid_from' => $date->copy()->startOfMonth(),
                        'valid_until' => $date->copy()->addYear()->endOfMonth(),
                        'is_active' => rand(0, 1) ? true : false,
                        'created_at' => $date->copy()->addDays(rand(0, 28))->setTime(rand(8, 23), rand(0, 59), rand(0, 59)),
                        'updated_at' => now(),
                    ]);

                    $userId++;
                } catch (\Exception $e) {
                    echo "⚠️ Error inserting membership for $monthLabel: " . $e->getMessage() . "\n";
                }
            }

            echo "✅ Added $count memberships for $monthLabel (User IDs: " . ($userId - $count) . " to " . ($userId - 1) . ")\n";
        }

        echo "\n🎉 Membership growth data seeding completed!\n";
        echo "📊 Total new memberships created: " . array_sum($membershipGrowthData) . "\n";
    }
}
