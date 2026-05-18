<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Extends Spatie's Permission model.
 *
 * New column
 * ──────────
 * is_system – true for built-in platform permissions that tenant roles
 *             may not be granted (e.g. courses.mark_global).
 *
 * Permissions have no employer_id/vendor_id because they are defined
 * at the platform level; only roles carry tenant scope.
 */
class Permission extends SpatiePermission
{
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    /**
     * Return all permissions keyed by their dot-prefix group.
     * e.g. "courses.view" → group "courses"
     *
     * @return array<string, Permission[]>
     */
    public static function grouped(): array
    {
        $groups = [];
        foreach (static::orderBy('name')->get() as $permission) {
            $group = explode('.', $permission->name)[0] ?? 'other';
            $groups[$group][] = $permission;
        }
        ksort($groups);
        return $groups;
    }
}
