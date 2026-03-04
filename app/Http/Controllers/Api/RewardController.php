<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\UserTransaction;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    /**
     * List all active rewards — includes per-user is_redeemed flag.
     */
    public function index()
    {
        $user = auth()->user();

        // Grab the IDs of rewards this user has already redeemed (one DB call)
        $redeemedIds = UserTransaction::where('user_id', $user->id)
            ->where('type', 'coins_spend')
            ->where('ref_type', 'reward')
            ->whereNotNull('ref_id')
            ->pluck('ref_id')
            ->map(fn($v) => (int)$v)
            ->all();

        $rewards = Reward::where('is_active', true)
            ->orderBy('cost_points')
            ->get()
            ->map(function ($r) use ($redeemedIds) {
                return [
                    'id'          => $r->id,
                    'title'       => $r->title,
                    'description' => $r->description,
                    'cost_points' => $r->cost_points,
                    'type'        => $r->type,
                    'stock'       => $r->stock,
                    'image'       => $r->image ? asset('storage/' . $r->image) : null,
                    // True when this user has already spent coins on this reward
                    'is_redeemed' => in_array((int)$r->id, $redeemedIds, true),
                ];
            });

        return response()->api('Rewards retrieved', $rewards);
    }

    /**
     * Redeem a reward (spend COINS).
     * Returns 409 when the reward was already redeemed so the client can
     * display the correct feedback without ambiguity.
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id',
        ]);

        $user   = auth()->user();
        $reward = Reward::findOrFail($request->reward_id);

        // ── Idempotency: same reward already redeemed by this user ────────────
        $alreadySpent = UserTransaction::where('user_id', $user->id)
            ->where('type', 'coins_spend')
            ->where('ref_type', 'reward')
            ->where('ref_id', (int)$reward->id)
            ->exists();

        if ($alreadySpent) {
            $existing = RewardRedemption::where('user_id', $user->id)
                ->where('reward_id', $reward->id)
                ->orderByDesc('created_at')
                ->first();

            // 409 Conflict: the client knows this is NOT a new redemption
            return response()->api('Reward already redeemed', [
                'redemption_id'   => $existing?->id,
                'remaining_coins' => (int)($user->fresh()->coins ?? 0),
                'is_redeemed'     => true,
            ], 409);
        }

        // ── Guard: reward must be active ──────────────────────────────────────
        if (!$reward->is_active) {
            return response()->api('Reward is not available', null, 400);
        }

        // ── Guard: enough coins ────────────────────────────────────────────────
        if ((int)($user->coins ?? 0) < (int)$reward->cost_points) {
            return response()->api('Not enough coins', [
                'required' => (int)$reward->cost_points,
                'balance'  => (int)($user->coins ?? 0),
            ], 400);
        }

        // ── Guard: stock available ─────────────────────────────────────────────
        if ($reward->stock !== null && $reward->stock <= 0) {
            return response()->api('Reward out of stock', null, 400);
        }

        // ── Deduct coins (atomic + idempotent ledger entry) ───────────────────
        $ok = GamificationService::spendCoins(
            $user,
            (int)$reward->cost_points,
            'reward_redeem',
            'reward',
            (int)$reward->id
        );

        if (!$ok) {
            // Race-condition safety: coins changed between check and decrement
            return response()->api('Not enough coins', null, 400);
        }

        // ── Decrement stock ────────────────────────────────────────────────────
        if ($reward->stock !== null) {
            $reward->decrement('stock');
        }

        // ── Create redemption record ──────────────────────────────────────────
        $redemption = RewardRedemption::create([
            'user_id'      => $user->id,
            'reward_id'    => $reward->id,
            'points_spent' => $reward->cost_points,
            'status'       => 'pending',
        ]);

        return response()->api('Reward redeemed successfully', [
            'redemption_id'   => $redemption->id,
            'remaining_coins' => (int)($user->fresh()->coins ?? 0),
            'is_redeemed'     => true,
        ]);
    }

    /**
     * My redemption history
     */
    public function myRedemptions()
    {
        $user = auth()->user();

        $redemptions = RewardRedemption::where('user_id', $user->id)
            ->with('reward')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id'           => $r->id,
                    'reward_id'    => $r->reward_id,
                    'reward_title' => $r->reward->title ?? '',
                    'points_spent' => $r->points_spent,
                    'status'       => $r->status,
                    'created_at'   => $r->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->api('My redemptions retrieved', $redemptions);
    }
}
