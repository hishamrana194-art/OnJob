<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\UserSavedReel;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class SavedReelController extends Controller
{
    /**
     * List all reels saved by the authenticated user.
     */
    public function index()
    {
        $user = auth()->user();

        $saved = UserSavedReel::where('user_id', $user->id)
            ->with('reel')
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn($s) => $s->reel && $s->reel->is_active)
            ->map(function ($s) {
                $r = $s->reel;
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
                    'is_saved'          => true,
                    'saved_at'          => $s->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->values();

        return response()->api('Saved reels retrieved', $saved);
    }

    /**
     * Toggle save/unsave for a reel.
     * Returns is_saved: true when saved, false when unsaved.
     */
    public function toggle(Request $request, string $reelId)
    {
        $user = auth()->user();
        $reel = Reel::find($reelId);

        if (!$reel) {
            return response()->api('Reel not found', null, 404);
        }

        $existing = UserSavedReel::where('user_id', $user->id)
            ->where('reel_id', $reel->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->api('Reel unsaved', ['is_saved' => false, 'reel_id' => $reel->id]);
        }

        UserSavedReel::create(['user_id' => $user->id, 'reel_id' => $reel->id]);
        return response()->api('Reel saved', ['is_saved' => true, 'reel_id' => $reel->id]);
    }
}
