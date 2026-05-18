<?php

namespace Database\Seeders;

use App\Models\Reel;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Database\Seeder;

/**
 * Seeds realistic XP and coin transactions for employer users so the
 * leaderboard, profile stats, and rewards screen all have test data.
 *
 * Logic:
 *  - Every employer_user gets transactions that match their `points` value.
 *  - Transactions are split across reels (to simulate real usage) plus a
 *    few manual "course_complete" grants for variety.
 */
class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('user_type', 'employer_user')->get();
        $reels = Reel::where('is_active', true)->get();

        foreach ($users as $user) {
            $remaining = $user->points;   // total XP we need to distribute
            $coins     = (int) ($user->points * 0.6); // coins ≈ 60 % of XP

            // --- XP transactions via reel completions ---
            foreach ($reels as $reel) {
                if ($remaining <= 0) break;

                $xp = min($reel->xp_on_complete ?? 10, $remaining);

                UserTransaction::firstOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'xp_earn',
                        'ref_type' => 'reel',
                        'ref_id'   => $reel->id,
                    ],
                    [
                        'amount' => $xp,
                        'reason' => 'Watched reel: ' . ($reel->title['en'] ?? 'Reel'),
                    ]
                );

                $remaining -= $xp;
            }

            // --- Coin transactions via reel completions ---
            $remainingCoins = $coins;
            foreach ($reels as $reel) {
                if ($remainingCoins <= 0) break;

                $coinAward = min($reel->coins_on_complete ?? 5, $remainingCoins);

                UserTransaction::firstOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'coins_earn',
                        'ref_type' => 'reel',
                        'ref_id'   => $reel->id,
                    ],
                    [
                        'amount' => $coinAward,
                        'reason' => 'Watched reel: ' . ($reel->title['en'] ?? 'Reel'),
                    ]
                );

                $remainingCoins -= $coinAward;
            }

            // --- Bonus XP for course completion (if any XP remaining) ---
            if ($remaining > 0) {
                UserTransaction::firstOrCreate(
                    [
                        'user_id'  => $user->id,
                        'type'     => 'xp_earn',
                        'ref_type' => 'course',
                        'ref_id'   => 1,
                    ],
                    [
                        'amount' => $remaining,
                        'reason' => 'Course completion bonus',
                    ]
                );
            }
        }

        $this->command->info('Seeded gamification transactions for ' . $users->count() . ' users.');
    }
}
