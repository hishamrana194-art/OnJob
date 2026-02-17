<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * Company leaderboard - users in the same employer/vendor
     */
    public function company()
    {
        $user = auth()->user();

        $query = User::query()->where('is_active', true);

        if ($user->employer_id) {
            $query->where('employer_id', $user->employer_id);
        } elseif ($user->vendor_id) {
            $query->where('vendor_id', $user->vendor_id);
        }

        $leaderboard = $query->orderByDesc('points')
            ->select('id', 'name', 'points', 'avatar')
            ->limit(50)
            ->get();

        $ranked = $this->addRanks($leaderboard, $user->id);

        return response()->api('Company leaderboard retrieved', $ranked);
    }

    /**
     * Global leaderboard - all users
     */
    public function global()
    {
        $user = auth()->user();

        $leaderboard = User::where('is_active', true)
            ->orderByDesc('points')
            ->select('id', 'name', 'points', 'avatar')
            ->limit(50)
            ->get();

        $ranked = $this->addRanks($leaderboard, $user->id);

        return response()->api('Global leaderboard retrieved', $ranked);
    }

    /**
     * My rank info
     */
    public function myRank()
    {
        $user = auth()->user();

        // Global rank
        $globalRank = User::where('is_active', true)
            ->where('points', '>', $user->points)
            ->count() + 1;

        // Company rank
        $companyRank = 0;
        $query = User::where('is_active', true);
        if ($user->employer_id) {
            $query->where('employer_id', $user->employer_id);
        } elseif ($user->vendor_id) {
            $query->where('vendor_id', $user->vendor_id);
        }
        $companyRank = $query->where('points', '>', $user->points)->count() + 1;

        return response()->api('My rank retrieved', [
            'global_rank' => $globalRank,
            'company_rank' => $companyRank,
            'points' => $user->points,
            'name' => $user->name,
        ]);
    }

    private function addRanks($leaderboard, $currentUserId)
    {
        $rank = 1;
        return $leaderboard->map(function ($item) use (&$rank, $currentUserId) {
            return [
                'rank' => $rank++,
                'id' => $item->id,
                'name' => $item->name,
                'points' => $item->points,
                'avatar' => $item->avatar ? asset('storage/' . $item->avatar) : null,
                'is_current_user' => $item->id === $currentUserId,
            ];
        })->values()->toArray();
    }
}
