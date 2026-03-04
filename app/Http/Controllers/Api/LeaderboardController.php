<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Company leaderboard — users in the same employer/vendor.
     */
    public function company()
    {
        $user  = auth()->user();
        $query = $this->scopeToOrg(User::query()->where('is_active', true), $user);

        $leaderboard = $this->fetchLeaderboard($query, 50);
        $ranked      = $this->buildRanked($leaderboard, $user->id);

        return response()->api('Company leaderboard retrieved', $ranked);
    }

    /**
     * Global leaderboard — all active users.
     */
    public function global()
    {
        $user        = auth()->user();
        $leaderboard = $this->fetchLeaderboard(User::where('is_active', true), 50);
        $ranked      = $this->buildRanked($leaderboard, $user->id);

        return response()->api('Global leaderboard retrieved', $ranked);
    }

    /**
     * Team leaderboard — users with the same role in the same org.
     */
    public function team()
    {
        $user  = auth()->user();
        $query = $this->scopeToOrg(User::query()->where('is_active', true), $user)
                      ->where('role', $user->role);

        $leaderboard = $this->fetchLeaderboard($query, 30);

        // Fallback: if team is empty return company board
        if ($leaderboard->isEmpty()) {
            $fallback    = $this->scopeToOrg(User::query()->where('is_active', true), $user);
            $leaderboard = $this->fetchLeaderboard($fallback, 30);
        }

        $ranked = $this->buildRanked($leaderboard, $user->id);

        return response()->api('Team leaderboard retrieved', $ranked);
    }

    /**
     * My rank info — global rank, company rank, full stats.
     */
    public function myRank()
    {
        $user  = auth()->user();
        $myXp  = (int)($user->xp ?? 0);

        $globalRank = User::where('is_active', true)
            ->whereRaw('COALESCE(xp, points, 0) > ?', [$myXp])
            ->count() + 1;

        $companyQuery = $this->scopeToOrg(User::where('is_active', true), $user);
        $companyRank  = $companyQuery
            ->whereRaw('COALESCE(xp, points, 0) > ?', [$myXp])
            ->count() + 1;

        return response()->api('My rank retrieved', [
            'global_rank'   => $globalRank,
            'company_rank'  => $companyRank,
            'xp'            => $myXp,
            'coins'         => (int)($user->coins  ?? 0),
            'level'         => GamificationService::levelFromXp($myXp),
            'badges_count'  => $user->badges()->count(),
            'name'          => $user->name,
            'avatar'        => $user->avatar ? asset('storage/' . $user->avatar) : null,
            // backward compat
            'points'        => $myXp,
        ]);
    }

    // ─────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────

    /** Restrict query to the authenticated user's organisation. */
    private function scopeToOrg($query, User $user)
    {
        if ($user->employer_id) {
            $query->where('employer_id', $user->employer_id);
        } elseif ($user->vendor_id) {
            $query->where('vendor_id', $user->vendor_id);
        }
        return $query;
    }

    /**
     * Fetch leaderboard rows with all gamification columns needed.
     * badges_count uses a correlated sub-query to avoid N+1.
     */
    private function fetchLeaderboard($query, int $limit)
    {
        return $query
            ->orderByRaw('COALESCE(xp, points, 0) DESC')
            ->select([
                'id',
                'name',
                'avatar',
                'role',
                DB::raw('COALESCE(xp, points, 0) as xp'),
                DB::raw('COALESCE(coins, 0) as coins'),
                DB::raw('(SELECT COUNT(*) FROM user_badges WHERE user_badges.user_id = users.id) as badges_count'),
            ])
            ->limit($limit)
            ->get();
    }

    /** Attach sequential rank and current-user flag, return plain array. */
    private function buildRanked($leaderboard, int $currentUserId): array
    {
        $rank = 1;
        return $leaderboard->map(function ($item) use (&$rank, $currentUserId) {
            $xp    = (int)($item->xp    ?? 0);
            $coins = (int)($item->coins ?? 0);
            return [
                'rank'           => $rank++,
                'id'             => $item->id,
                'name'           => $item->name,
                'xp'             => $xp,
                'coins'          => $coins,
                'level'          => GamificationService::levelFromXp($xp),
                'badges_count'   => (int)($item->badges_count ?? 0),
                'avatar'         => $item->avatar ? asset('storage/' . $item->avatar) : null,
                'is_current_user'=> $item->id === $currentUserId,
                // backward compat
                'points'         => $xp,
            ];
        })->values()->toArray();
    }
}
