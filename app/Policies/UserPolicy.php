<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $authUser): bool
    {
        // Super admin can view all users
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        // Employer admin can view their users
        if ($authUser->user_type === 'employer_user') {
            return true;
        }

        // Vendor admin can view their users
        if ($authUser->user_type === 'vendor_user') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authUser, User $user): bool
    {
        // Can always view self
        if ($authUser->id === $user->id) {
            return true;
        }

        // Super admin can view any user
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        // Employer user can view users in their organization
        if ($authUser->user_type === 'employer_user') {
            return $user->employer_id === $authUser->employer_id;
        }

        // Vendor user can view users in their organization
        if ($authUser->user_type === 'vendor_user') {
            return $user->vendor_id === $authUser->vendor_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $authUser): bool
    {
        // Super admin can create any type of user
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        // Employer admin can create employer users
        if ($authUser->user_type === 'employer_user' && $authUser->role === 'employer_admin') {
            return true;
        }

        // Vendor admin can create vendor users
        if ($authUser->user_type === 'vendor_user' && $authUser->role === 'vendor_admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $user): bool
    {
        // Cannot update other super admins
        if ($this->isSuperAdmin($user) && $authUser->id !== $user->id) {
            return false;
        }

        // Can always update self
        if ($authUser->id === $user->id) {
            return true;
        }

        // Super admin can update any user
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        // Employer admin can update users in their organization
        if ($authUser->user_type === 'employer_user' && $authUser->role === 'employer_admin') {
            return $user->employer_id === $authUser->employer_id;
        }

        // Vendor admin can update users in their organization
        if ($authUser->user_type === 'vendor_user' && $authUser->role === 'vendor_admin') {
            return $user->vendor_id === $authUser->vendor_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, User $user): bool
    {
        // Cannot delete self
        if ($authUser->id === $user->id) {
            return false;
        }

        // Cannot delete super admins
        if ($this->isSuperAdmin($user)) {
            return false;
        }

        // Super admin can delete any user
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        // Employer admin can delete users in their organization
        if ($authUser->user_type === 'employer_user' && $authUser->role === 'employer_admin') {
            return $user->employer_id === $authUser->employer_id;
        }

        // Vendor admin can delete users in their organization
        if ($authUser->user_type === 'vendor_user' && $authUser->role === 'vendor_admin') {
            return $user->vendor_id === $authUser->vendor_id;
        }

        return false;
    }

    /**
     * Check if user is super admin
     */
    protected function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super_admin') || !$user->user_type;
    }
}

