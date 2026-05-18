<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Extends Spatie's Role model with multi-tenant support.
 *
 * New columns
 * ───────────
 * is_system   – true for built-in platform roles (read-only via API)
 * employer_id – scopes role to one employer (null = platform-wide)
 * vendor_id   – scopes role to one vendor   (null = platform-wide)
 *
 * NOTE: `is_system` is deliberately kept out of $fillable so it can only
 * be set through migrations/seeders, never through user-supplied payloads.
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'employer_id',
        'vendor_id',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Filter roles visible to a given caller.
     *
     * | Caller                        | Sees                                      |
     * |-------------------------------|-------------------------------------------|
     * | Admin model (super-admin)     | all roles                                 |
     * | User – no user_type           | all roles (treated as platform super-user)|
     * | employer_admin                | system roles + their employer's roles     |
     * | vendor_admin                  | system roles + their vendor's roles       |
     * | other authenticated users     | system roles only                         |
     */
    public function scopeForUser(Builder $query, $user = null): Builder
    {
        // Admin model or null → no filter (full list)
        if ($user === null || $user instanceof Admin) {
            return $query;
        }

        if (!($user instanceof User)) {
            return $query;
        }

        // User with no user_type = platform-level user → full access
        if (empty($user->user_type)) {
            return $query;
        }

        return match ($user->user_type) {
            'employer_user' => $query->where(function (Builder $q) use ($user) {
                $q->where('is_system', true)
                  ->orWhere('employer_id', $user->employer_id);
            }),
            'vendor_user' => $query->where(function (Builder $q) use ($user) {
                $q->where('is_system', true)
                  ->orWhere('vendor_id', $user->vendor_id);
            }),
            default => $query->where('is_system', true),
        };
    }
}
