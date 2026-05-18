<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;

/**
 * Authorization rules for Role management.
 *
 * | Actor                  | viewAny | create | update/delete          |
 * |------------------------|---------|--------|------------------------|
 * | Admin model            | ✓       | ✓      | ✓ (all roles)          |
 * | User (no user_type)    | ✓       | ✓      | ✓ (all non-system)     |
 * | employer_admin         | ✓       | ✓      | own employer roles only|
 * | vendor_admin           | ✓       | ✓      | own vendor roles only  |
 * | other User             | ✓       | ✗      | ✗                      |
 *
 * System roles (is_system = true) can NEVER be modified or deleted.
 */
class RolePolicy
{
    /**
     * Admin model (admins table) bypasses all checks — full access.
     * No type-hint on $user to avoid TypeError when Admin is passed.
     */
    public function before($user, string $ability): ?bool
    {
        if ($user instanceof Admin) {
            return true;
        }
        return null;
    }

    /**
     * Any authenticated user may list roles (the query is scoped in the controller).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Platform-level users (no user_type) and tenant admins may create roles.
     */
    public function create(User $user): bool
    {
        if (empty($user->user_type)) {
            return true; // platform-level user
        }

        return $this->isTenantAdmin($user);
    }

    /**
     * A role may be updated if:
     *   1. It is NOT a system role.
     *   2. The caller owns the tenant the role belongs to.
     */
    public function update(User $user, Role $role): bool
    {
        if ($role->is_system) {
            return false;
        }

        if (empty($user->user_type)) {
            return true; // platform-level user can edit any non-system role
        }

        return $this->ownsTenantRole($user, $role);
    }

    /**
     * Deletion follows the same rules as update.
     */
    public function delete(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function isTenantAdmin(User $user): bool
    {
        return ($user->user_type === 'employer_user'
                && ($user->role === 'employer_admin' || $user->hasRole('employer_admin')))
            || ($user->user_type === 'vendor_user'
                && ($user->role === 'vendor_admin' || $user->hasRole('vendor_admin')));
    }

    private function ownsTenantRole(User $user, Role $role): bool
    {
        if ($user->user_type === 'employer_user'
            && ($user->role === 'employer_admin' || $user->hasRole('employer_admin'))
        ) {
            return (int) $role->employer_id === (int) $user->employer_id;
        }

        if ($user->user_type === 'vendor_user'
            && ($user->role === 'vendor_admin' || $user->hasRole('vendor_admin'))
        ) {
            return (int) $role->vendor_id === (int) $user->vendor_id;
        }

        return false;
    }
}
