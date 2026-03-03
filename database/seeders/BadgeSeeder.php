<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        // `type` is used as a stable code/slug.
        $badges = [
            [
                'type' => 'first_lesson',
                'title' => ['ar' => 'أول درس', 'en' => 'First Lesson'],
                'description' => ['ar' => 'أكملت أول درس لك', 'en' => 'Completed your first lesson'],
                'icon' => null,
                'is_active' => true,
            ],
            [
                'type' => 'first_quiz',
                'title' => ['ar' => 'أول اختبار', 'en' => 'First Quiz'],
                'description' => ['ar' => 'نجحت في أول اختبار', 'en' => 'Passed your first quiz'],
                'icon' => null,
                'is_active' => true,
            ],
            [
                'type' => 'xp_100',
                'title' => ['ar' => '100 خبرة', 'en' => '100 XP'],
                'description' => ['ar' => 'وصلت إلى 100 نقطة خبرة', 'en' => 'Reached 100 XP'],
                'icon' => null,
                'is_active' => true,
            ],
        ];

        foreach ($badges as $b) {
            Badge::updateOrCreate(['type' => $b['type']], $b);
        }
    }
}
