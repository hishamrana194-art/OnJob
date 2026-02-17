<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRedemption;
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
                    'cost_points' => $r->cost_points,
                    'type' => $r->type,
                    'stock' => $r->stock,
                    'image' => $r->image ? asset('storage/' . $r->image) : null,
                ];
            });

        return response()->api('Rewards retrieved', $rewards);
    }

    /**
     * Redeem a reward
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id',
        ]);

        $user = auth()->user();
        $reward = Reward::findOrFail($request->reward_id);

        if (!$reward->is_active) {
            return response()->api('Reward is not available', null, 400);
        }

        if ($user->points < $reward->cost_points) {
            return response()->api('Not enough points', null, 400);
        }

        if ($reward->stock !== null && $reward->stock <= 0) {
            return response()->api('Reward out of stock', null, 400);
        }

        // Deduct points
        $user->decrement('points', $reward->cost_points);

        // Decrement stock if tracked
        if ($reward->stock !== null) {
            $reward->decrement('stock');
        }

        // Create redemption record
        $redemption = RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_spent' => $reward->cost_points,
            'status' => 'pending',
        ]);

        return response()->api('Reward redeemed successfully', [
            'redemption_id' => $redemption->id,
            'remaining_points' => $user->fresh()->points,
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
