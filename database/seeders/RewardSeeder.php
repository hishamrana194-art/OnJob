<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reward;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal default rewards so the Rewards screen is never empty.
        // Costs are in COINS (kept as cost_points for backward compatibility).
        $defaults = [
            [
                'title' => ['ar' => 'قسيمة قهوة', 'en' => 'Coffee Voucher'],
                'description' => ['ar' => 'قسيمة قهوة مجانية', 'en' => 'Free coffee voucher'],
                'cost_points' => 20,
                'type' => 'voucher',
                'stock' => null,
                'is_active' => true,
            ],
            [
                'title' => ['ar' => 'يوم إجازة', 'en' => 'Day Off'],
                'description' => ['ar' => 'استبدلها بيوم إجازة', 'en' => 'Redeem for a day off'],
                'cost_points' => 200,
                'type' => 'benefit',
                'stock' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($defaults as $d) {
            Reward::updateOrCreate(
                ['type' => $d['type'], 'cost_points' => $d['cost_points']],
                $d
            );
        }
    }
}
