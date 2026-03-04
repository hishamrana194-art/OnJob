<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class ReelController extends Controller
{
    /**
     * List all active reels
     */
    public function index()
    {
        $reels = Reel::where('is_active', true)
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id'               => $r->id,
                    'title'            => $r->title,
                    'description'      => $r->description,
                    'video_url'        => $r->video_url,
                    'duration'         => $r->duration,
                    'thumbnail_url'    => $r->thumbnail_url ? asset('storage/' . $r->thumbnail_url) : null,
                    'course_id'        => $r->course_id,
                    'quiz_id'          => $r->quiz_id,
                    // Inform the app how many coins/XP this reel rewards so it can show a preview
                    'coins_on_complete' => $r->coins_on_complete ?? GamificationService::COINS_VIDEO_COMPLETE,
                    'xp_on_complete'    => $r->xp_on_complete    ?? GamificationService::XP_VIDEO_COMPLETE,
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

        // Idempotent: will silently skip if already awarded for this reel
        GamificationService::awardXp($user,    $xpToAward,    'reel_complete', 'reel', (int)$reel->id);
        GamificationService::awardCoins($user, $coinsToAward, 'reel_complete', 'reel', (int)$reel->id);

        $freshUser = $user->fresh();

        return response()->api('Reel marked as complete', [
            'reel_id'      => $reel->id,
            'xp_earned'    => $xpToAward,
            'coins_earned' => $coinsToAward,
            'xp'           => (int)($freshUser->xp    ?? 0),
            'coins'        => (int)($freshUser->coins  ?? 0),
            // Legacy
            'points'       => (int)($freshUser->xp    ?? 0),
        ]);
    }
}
