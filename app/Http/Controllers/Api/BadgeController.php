<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * My earned badges
     */
    public function myBadges()
    {
        $user = auth()->user();

        $badges = $user->badges()
            ->orderByPivot('earned_at', 'desc')
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'title' => $badge->title,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'type' => $badge->type,
                    'earned_at' => $badge->pivot->earned_at,
                ];
            });

        return response()->api('My badges retrieved', $badges);
    }

    /**
     * All available badges
     */
    public function index()
    {
        $badges = Badge::where('is_active', true)->get()->map(function ($b) {
            return [
                'id' => $b->id,
                'title' => $b->title,
                'description' => $b->description,
                'icon' => $b->icon,
                'type' => $b->type,
            ];
        });

        return response()->api('All badges retrieved', $badges);
    }
}
