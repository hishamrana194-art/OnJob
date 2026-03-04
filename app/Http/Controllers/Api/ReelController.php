<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\UserSavedReel;
use App\Models\UserTransaction;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class ReelController extends Controller
{
    /**
     * List all active reels
     */
    public function index()
    {
        $user = auth()->user();

        // One query: all reel IDs this user has saved
        $savedIds = UserSavedReel::where('user_id', $user->id)
            ->pluck('reel_id')
            ->map(fn($v) => (int)$v)
            ->all();

        $reels = Reel::where('is_active', true)
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) use ($savedIds) {
                return [
                    'id'               => $r->id,
                    'title'            => $r->title,
                    'description'      => $r->description,
                    'video_url'        => $r->video_url,
                    'duration'         => $r->duration,
                    'thumbnail_url'    => $r->thumbnail_url ? asset('storage/' . $r->thumbnail_url) : null,
                    'course_id'        => $r->course_id,
                    'quiz_id'          => $r->quiz_id,
                    'coins_on_complete' => $r->coins_on_complete ?? GamificationService::COINS_VIDEO_COMPLETE,
                    'xp_on_complete'    => $r->xp_on_complete    ?? GamificationService::XP_VIDEO_COMPLETE,
                    'is_saved'          => in_array((int)$r->id, $savedIds, true),
                ];
            });

        return response()->api('Reels retrieved', $reels);
    }

    /**
     * Mark reel as watched/completed — awards XP + Coins once per reel per user.
     */
    public function complete(string $id)
    {
        $reel = Reel::find($id);

        if (!$reel) {
            return response()->api('Reel not found', null, 404);
        }

        $user = auth()->user();

        $coinsToAward = $reel->coins_on_complete ?? GamificationService::COINS_VIDEO_COMPLETE;
        $xpToAward    = $reel->xp_on_complete    ?? GamificationService::XP_VIDEO_COMPLETE;

        // Check before awarding so we can return an accurate already_rewarded flag
        $alreadyRewarded = UserTransaction::where('user_id', $user->id)
            ->where('type', 'xp_earn')
            ->where('ref_type', 'reel')
            ->where('ref_id', (int)$reel->id)
            ->exists();

        // Idempotent: will silently skip if already awarded for this reel
        GamificationService::awardXp($user,    $xpToAward,    'reel_complete', 'reel', (int)$reel->id);
        GamificationService::awardCoins($user, $coinsToAward, 'reel_complete', 'reel', (int)$reel->id);

        $freshUser = $user->fresh();

        return response()->api('Reel marked as complete', [
            'reel_id'        => $reel->id,
            'xp_earned'      => $xpToAward,
            'coins_earned'   => $coinsToAward,
            'already_rewarded' => $alreadyRewarded,
            'xp'             => (int)($freshUser->xp    ?? 0),
            'coins'          => (int)($freshUser->coins  ?? 0),
            // Legacy
            'points'         => (int)($freshUser->xp    ?? 0),
        ]);
    }
}
