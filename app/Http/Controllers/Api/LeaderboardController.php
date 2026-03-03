<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Support\Facades\DB;
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

        $leaderboard = $query->orderByRaw('COALESCE(xp, points, 0) DESC')
            ->select('id','name', DB::raw("COALESCE(xp, points, 0) as xp"), 'avatar')
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
            ->orderByRaw('COALESCE(xp, points, 0) DESC')
            ->select('id','name', DB::raw("COALESCE(xp, points, 0) as xp"), 'avatar')
            ->limit(50)
            ->get();

        $ranked = $this->addRanks($leaderboard, $user->id);

        return response()->api('Global leaderboard retrieved', $ranked);
    }

    /**
     * Team leaderboard - users with the same role in the same employer
     */
    public function team()
    {
        $user = auth()->user();

        $query = User::query()->where('is_active', true);

        if ($user->employer_id) {
            $query->where('employer_id', $user->employer_id)
                ->where('role', $user->role);
        } elseif ($user->vendor_id) {
            $query->where('vendor_id', $user->vendor_id)
                ->where('role', $user->role);
        }

        $leaderboard = $query->orderByRaw('COALESCE(xp, points, 0) DESC')
            ->select('id','name', DB::raw("COALESCE(xp, points, 0) as xp"), 'avatar', 'role')
            ->limit(30)
            ->get();

        // Fallback: if team is empty, return same company
        if ($leaderboard->isEmpty()) {
            $fallbackQuery = User::query()->where('is_active', true);
            if ($user->employer_id) {
                $fallbackQuery->where('employer_id', $user->employer_id);
            } elseif ($user->vendor_id) {
                $fallbackQuery->where('vendor_id', $user->vendor_id);
            }
            $leaderboard = $fallbackQuery->orderByRaw('COALESCE(xp, points, 0) DESC')
                ->select('id','name', DB::raw("COALESCE(xp, points, 0) as xp"), 'avatar', 'role')
                ->limit(30)
                ->get();
        }

        $ranked = $this->addRanks($leaderboard, $user->id);

        return response()->api('Team leaderboard retrieved', $ranked);
    }

    /**
     * My rank info (based on XP)
     */
    public function myRank()
    {
        $user = auth()->user();

        $myXp = (int)($user->xp ?? 0);

        // Global rank
        $globalRank = User::where('is_active', true)
            ->where('xp', '>', $myXp)
            ->count() + 1;

        // Company rank
        $query = User::where('is_active', true);
        if ($user->employer_id) {
            $query->where('employer_id', $user->employer_id);
        } elseif ($user->vendor_id) {
            $query->where('vendor_id', $user->vendor_id);
        }
        $companyRank = $query->where('xp', '>', $myXp)->count() + 1;

        return response()->api('My rank retrieved', [
            'global_rank' => $globalRank,
            'company_rank' => $companyRank,
            'xp' => $myXp,
            // Backward compatibility
            'points' => $myXp,
            'name' => $user->name,
        ]);
    }

    private function addRanks($leaderboard, $currentUserId)
    {
        $rank = 1;
        return $leaderboard->map(function ($item) use (&$rank, $currentUserId) {
            $xp = (int)($item->xp ?? 0);
            return [
                'rank' => $rank++,
                'id' => $item->id,
                'name' => $item->name,
                'xp' => $xp,
                // Backward compatibility
                'points' => $xp,
                'avatar' => $item->avatar ? asset('storage/' . $item->avatar) : null,
                'is_current_user' => $item->id === $currentUserId,
            ];
        })->values()->toArray();
    }
}
