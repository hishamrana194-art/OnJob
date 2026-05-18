<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Fast-path for Admin model (from the `admins` table).
     *
     * Admin instances do NOT use Spatie HasRoles, so permission checks would
     * throw. Return true unconditionally — admins have full course access.
     *
     * Returning null lets normal policy methods run for every other user type.
     *
     * NOTE: No type-hint on $user — if we type-hint `User` here, PHP throws a
     *       TypeError when an Admin object is passed.
     */
    public function before($user, string $ability): ?bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        return null; // continue to the individual methods
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Read
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Can this user view a specific course?
     *
     * This mirrors the query-level filter in scopeForUser(). It is used by
     * show() to guard direct-ID lookups that bypass the listing query.
     *
     * | Caller type     | Allowed courses                          |
     * |-----------------|------------------------------------------|
     * | employer_user   | Only global courses                      |
     * | vendor_user     | Only their own vendor's courses          |
     * | training_provider / super_admin | Any course              |
     */
    public function view(User $user, Course $course): bool
    {
        return match ($user->user_type) {
            'employer_user' => $course->is_global,
            'vendor_user'   => !$course->is_global
                               && $course->vendor_id === (int) $user->vendor_id,
            default         => true,
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Write
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Can this user create a course?
     *
     * Employer users consume courses; they do not author them.
     */
    public function create(User $user): bool
    {
        return $user->user_type !== 'employer_user';
    }

    /**
     * Can this user update a course?
     *
     * Vendor users may only update courses that belong to their own vendor.
     * course_admin / training_provider / super_admin have unrestricted access.
     */
    public function update(User $user, Course $course): bool
    {
        if ($user->user_type === 'employer_user') {
            return false;
        }

        if ($user->user_type === 'vendor_user') {
            return $course->vendor_id === (int) $user->vendor_id;
        }

        return true; // training_provider, course_admin, super_admin
    }

    /**
     * Can this user delete a course?
     *
     * Ownership rules are identical to update.
     */
    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Special ability
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Can this user mark a course as global (is_global = true)?
     *
     * This is a distinct permission from generic course editing because making
     * a course global exposes it to ALL employer users platform-wide.
     *
     * Only users who have been explicitly granted the 'courses.mark_global'
     * Spatie permission (i.e. the course_admin role) may do this.
     */
    public function markGlobal(User $user): bool
    {
        return $user->hasPermissionTo('courses.mark_global');
    }
}
