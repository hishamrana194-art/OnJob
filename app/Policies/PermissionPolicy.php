<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;

/**
 * Permissions are a read-only, platform-defined catalogue.
 * Any authenticated actor may list them; no one may create/edit/delete
 * permissions via the API.
 */
class PermissionPolicy
{
    /**
     * Admin model → full access.
     */
    public function before($user, string $ability): ?bool
    {
        if ($user instanceof Admin) {
            return true;
        }
        return null;
    }

    /**
     * Any authenticated user may view the permissions catalogue.
     * The controller filters which permissions are exposed based on the caller.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}
