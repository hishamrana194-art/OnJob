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
     * List all active rewards
     */
    public function index()
    {
        $rewards = Reward::where('is_active', true)
            ->orderBy('cost_points')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'description' => $r->description,
                    // Keeping key name for backward compatibility with the Flutter app.
                    // Semantically: cost in COINS.
                    'cost_points' => $r->cost_points,
                    'type' => $r->type,
                    'stock' => $r->stock,
                    'image' => $r->image ? asset('storage/' . $r->image) : null,
                ];
            });

        return response()->api('Rewards retrieved', $rewards);
    }

    /**
     * Redeem a reward (spend COINS)
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id',
        ]);

        $user = auth()->user();
        $reward = Reward::findOrFail($request->reward_id);

        // Idempotency: if the same reward was already redeemed (double tap / retry),
        // return the previous redemption instead of throwing 500.
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

            return response()->api('Reward already redeemed', [
                'redemption_id' => $existing?->id,
                'remaining_coins' => (int)($user->fresh()->coins ?? 0),
            ]);
        }

        if (!$reward->is_active) {
            return response()->api('Reward is not available', null, 400);
        }

        if ((int)($user->coins ?? 0) < (int)$reward->cost_points) {
            return response()->api('Not enough coins', null, 400);
        }

        if ($reward->stock !== null && $reward->stock <= 0) {
            return response()->api('Reward out of stock', null, 400);
        }

        // Deduct coins (atomic + ledger)
        $ok = GamificationService::spendCoins($user, (int)$reward->cost_points, 'reward_redeem', 'reward', (int)$reward->id);
        if (!$ok) {
            return response()->api('Not enough coins', null, 400);
        }

        // Decrement stock if tracked
        if ($reward->stock !== null) {
            $reward->decrement('stock');
        }

        // Create redemption record
        $redemption = RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_spent' => $reward->cost_points, // keeping column name
            'status' => 'pending',
        ]);

        return response()->api('Reward redeemed successfully', [
            'redemption_id' => $redemption->id,
            'remaining_coins' => (int)($user->fresh()->coins ?? 0),
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
                    'id' => $r->id,
                    'reward_title' => $r->reward->title ?? '',
                    'points_spent' => $r->points_spent,
                    'status' => $r->status,
                    'created_at' => $r->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->api('My redemptions retrieved', $redemptions);
    }
}
