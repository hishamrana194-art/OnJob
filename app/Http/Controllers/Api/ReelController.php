<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reel;
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
                    'id' => $r->id,
                    'title' => $r->title,
                    'description' => $r->description,
                    'video_url' => $r->video_url,
                    'duration' => $r->duration,
                    'thumbnail_url' => $r->thumbnail_url ? asset('storage/' . $r->thumbnail_url) : null,
                    'course_id' => $r->course_id,
                    'quiz_id' => $r->quiz_id,
                ];
            });

        return response()->api('Reels retrieved', $reels);
    }

    /**
     * Mark reel as watched/completed
     */
    public function complete(string $id)
    {
        $reel = Reel::find($id);

        if (!$reel) {
            return response()->api('Reel not found', null, 404);
        }

        $user = auth()->user();

        return response()->api('Reel marked as complete', [
            'reel_id' => $reel->id,
            'points' => $user->points,
        ]);
    }
}
