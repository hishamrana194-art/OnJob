<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserModuleItemProgress;
use App\Models\UserQuizAttempt;

class BadgeService
{
    /**
     * Award a badge by its `type` code if the user doesn't have it yet.
     */
    public static function award(User $user, string $type, ?string $contextCourseTitle = null): void
    {
        $badge = Badge::where('type', $type)->where('is_active', true)->first();
        if (!$badge) return;

        $has = $user->badges()->where('badge_id', $badge->id)->exists();
        if ($has) return;

        $user->badges()->attach($badge->id, ['earned_at' => now()]);

        // Send in-app notification (stored in database) so it appears in notifications screen.
        try {
            $locale = $user->locale ?? 'ar';

            // Our badges are stored in `title` (translatable). Keep a safe fallback.
            $badgeName = method_exists($badge, 'getTranslation')
                ? ($badge->getTranslation('title', $locale) ?: $badge->getTranslation('title', 'ar') ?: $badge->getTranslation('title', 'en') ?: 'Badge')
                : ($badge->title['ar'] ?? $badge->title['en'] ?? 'Badge');

            $badgeDesc = is_array($badge->description)
                ? ($badge->description[$user->locale ?? 'ar'] ?? $badge->description['ar'] ?? $badge->description['en'] ?? null)
                : ($badge->description ?? null);

            $user->notify(new \App\Notifications\BadgeUnlockedNotification(
                $badge->type,
                $badgeName,
                $badgeDesc,
                $contextCourseTitle,
            ));
        } catch (\Throwable $e) {
            // Ignore notification failures
        }
    }

    /**
     * Check simple, safe rules after learning events.
     */
    public static function checkAfterLearningEvent(User $user): void
    {
        // 1) First lesson
        $completedItems = UserModuleItemProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();
        if ($completedItems >= 1) {
            self::award($user, 'first_lesson');
        }

        // 2) First quiz pass
        $passedQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('is_passed', true)
            ->count();
        if ($passedQuizzes >= 1) {
            self::award($user, 'first_quiz');
        }

        // 3) XP milestone(s)
        $xp = (int)($user->fresh()->xp ?? 0);
        if ($xp >= 100) {
            self::award($user, 'xp_100');
        }
    }
}
