<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    // Default rules (can be moved to config later)
    public const XP_VIDEO_COMPLETE = 20;
    public const XP_PDF_COMPLETE   = 15;
    public const XP_QUIZ_PASS      = 50;

    // Small coins rewards to make redeem loop feel alive.
    // Quiz still gives the biggest coins reward.
    public const COINS_VIDEO_COMPLETE = 2;
    public const COINS_PDF_COMPLETE   = 1;
    public const COINS_QUIZ_PASS   = 10;

    /**
     * Simple level mapping based on XP.
     * Keep in one place so backend + clients stay consistent.
     */
    public static function levelFromXp(int $xp): string
    {
        if ($xp < 100) return 'level_beginner_value';
        if ($xp < 300) return 'level_intermediate_value';
        if ($xp < 600) return 'level_advanced_value';
        if ($xp < 1000) return 'level_expert_value';
        return 'level_master_value';
    }

    public static function awardXp(User $user, int $amount, ?string $reason = null, ?string $refType = null, ?int $refId = null): bool
    {
        // XP is the experience/leaderboard currency.
        // Coins (spendable points) are awarded separately via awardCoins() so each
        // has its own idempotency slot and correct amount.
        return self::award($user, 'xp_earn', $amount, $reason, $refType, $refId);
    }

    public static function awardCoins(User $user, int $amount, ?string $reason = null, ?string $refType = null, ?int $refId = null): bool
    {
        return self::award($user, 'coins_earn', $amount, $reason, $refType, $refId);
    }

    public static function spendCoins(User $user, int $amount, ?string $reason = null, ?string $refType = null, ?int $refId = null): bool
    {
        return DB::transaction(function () use ($user, $amount, $reason, $refType, $refId) {
            if ($amount <= 0) return true;
            $user->refresh();
            if (($user->coins ?? 0) < $amount) {
                return false;
            }

            // Idempotency: if the same spend transaction already exists (e.g. double tap),
            // do NOT throw. Just treat it as already processed.
            if ($refType && $refId) {
                $exists = UserTransaction::where('user_id', $user->id)
                    ->where('type', 'coins_spend')
                    ->where('ref_type', $refType)
                    ->where('ref_id', $refId)
                    ->exists();
                if ($exists) {
                    return true;
                }
            }

            // Create txn first to enforce uniqueness (if needed)
            UserTransaction::create([
                'user_id' => $user->id,
                'type' => 'coins_spend',
                'amount' => $amount,
                'reason' => $reason,
                'ref_type' => $refType,
                'ref_id' => $refId,
            ]);

            $user->decrement('coins', $amount);
            return true;
        });
    }

    private static function award(User $user, string $type, int $amount, ?string $reason, ?string $refType, ?int $refId): bool
    {
        return DB::transaction(function () use ($user, $type, $amount, $reason, $refType, $refId) {
            if ($amount <= 0) return true;

            // If a txn exists for same ref, do nothing (prevents duplicates)
            if ($refType && $refId) {
                $exists = UserTransaction::where('user_id', $user->id)
                    ->where('type', $type)
                    ->where('ref_type', $refType)
                    ->where('ref_id', $refId)
                    ->exists();
                if ($exists) {
                    return false;
                }
            }

            UserTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'reason' => $reason,
                'ref_type' => $refType,
                'ref_id' => $refId,
            ]);

            if ($type === 'xp_earn') {
                $user->increment('xp', $amount);
            } elseif ($type === 'coins_earn') {
                $user->increment('coins', $amount);
            }

            // Badge checks are safe & idempotent.
            try {
                \App\Services\BadgeService::checkAfterLearningEvent($user);
            } catch (\Throwable $e) {
                // Do not fail gamification if badge logic fails
            }

            return true;
        });
    }
}
