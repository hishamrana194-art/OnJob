<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // XP/Coins are the new gamification currencies.
        // Keep legacy `points` for backward compatibility with older clients.
        $xp = (int)($this->xp ?? 0);
        $coins = (int)($this->coins ?? 0);
        $level = \App\Services\GamificationService::levelFromXp($xp);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'user_type' => $this->user_type,

            // Employer (only for employer_user)
            'employer' => $this->when($this->user_type === 'employer_user' && $this->employer, function () {
                return [
                    'id' => $this->employer?->id,
                    'name' => $this->employer?->name,
                ];
            }),

            // Vendor (only for vendor_user)
            'vendor' => $this->when($this->user_type === 'vendor_user' && $this->vendor, function () {
                return [
                    'id' => $this->vendor?->id,
                    'name' => $this->vendor?->name,
                ];
            }),

            // Owner - unified accessor for employer or vendor
            'owner' => [
                'type' => $this->user_type,
                'id' => $this->owner?->id,
                'name' => $this->owner_name,
            ],

            'employer_id' => $this->employer_id,
            'vendor_id' => $this->vendor_id,
            'role' => $this->role,

            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),

            'permissions' => $this->whenLoaded('roles', function () {
                return $this->getAllPermissions()->pluck('name');
            }),

            // New fields
            'xp' => $xp,
            'coins' => $coins,
            'level' => $level,

            // Legacy (treat as XP)
            'points' => $xp,
            'locale' => $this->locale,
            'is_active' => (bool) $this->is_active,
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

