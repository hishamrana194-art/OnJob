<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // Learning milestones
            [
                'type'        => 'first_lesson',
                'title'       => ['ar' => 'أول درس', 'en' => 'First Lesson'],
                'description' => ['ar' => 'أكملت أول درس لك', 'en' => 'Completed your first lesson'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'first_quiz',
                'title'       => ['ar' => 'أول اختبار', 'en' => 'First Quiz'],
                'description' => ['ar' => 'نجحت في أول اختبار', 'en' => 'Passed your first quiz'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'course_completed',
                'title'       => ['ar' => 'منهي الدورة', 'en' => 'Course Completer'],
                'description' => ['ar' => 'أكملت دورة كاملة بنجاح', 'en' => 'Successfully completed a full course'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'first_reel',
                'title'       => ['ar' => 'أول مقطع', 'en' => 'First Reel'],
                'description' => ['ar' => 'شاهدت أول مقطع تعليمي', 'en' => 'Watched your first learning reel'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'reel_watcher_5',
                'title'       => ['ar' => 'مشاهد نشيط', 'en' => 'Active Viewer'],
                'description' => ['ar' => 'شاهدت 5 مقاطع تعليمية', 'en' => 'Watched 5 learning reels'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'reel_watcher_15',
                'title'       => ['ar' => 'مشاهد محترف', 'en' => 'Reel Pro'],
                'description' => ['ar' => 'شاهدت 15 مقطعًا تعليميًا', 'en' => 'Watched 15 learning reels'],
                'icon'        => null,
                'is_active'   => true,
            ],
            // XP milestones
            [
                'type'        => 'xp_100',
                'title'       => ['ar' => '100 نقطة خبرة', 'en' => '100 XP'],
                'description' => ['ar' => 'وصلت إلى 100 نقطة خبرة', 'en' => 'Reached 100 XP'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'xp_500',
                'title'       => ['ar' => '500 نقطة خبرة', 'en' => '500 XP'],
                'description' => ['ar' => 'وصلت إلى 500 نقطة خبرة', 'en' => 'Reached 500 XP'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'xp_1000',
                'title'       => ['ar' => 'الف نقطة خبرة', 'en' => '1000 XP'],
                'description' => ['ar' => 'وصلت إلى 1000 نقطة خبرة - رائع!', 'en' => 'Reached 1000 XP - Amazing!'],
                'icon'        => null,
                'is_active'   => true,
            ],
            // Community
            [
                'type'        => 'team_player',
                'title'       => ['ar' => 'لاعب فريق', 'en' => 'Team Player'],
                'description' => ['ar' => 'شاركت مقطعًا مع فريقك', 'en' => 'Shared a reel with your team'],
                'icon'        => null,
                'is_active'   => true,
            ],
            [
                'type'        => 'top_learner',
                'title'       => ['ar' => 'أفضل متعلم', 'en' => 'Top Learner'],
                'description' => ['ar' => 'وصلت إلى المركز الأول في لوحة المتصدرين', 'en' => 'Reached #1 on the leaderboard'],
                'icon'        => null,
                'is_active'   => true,
            ],
        ];

        foreach ($badges as $b) {
            Badge::updateOrCreate(['type' => $b['type']], $b);
        }

        $this->command->info('Seeded ' . count($badges) . ' badges.');
    }
}
