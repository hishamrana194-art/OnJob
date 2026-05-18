<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Define all platform permissions ───────────────────────────────
        //
        // Permissions are always system-level; only roles carry tenant scope.
        // `courses.mark_global` is a special system-only permission — tenant
        // roles may never be granted it (enforced in RoleController).

        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Course Management
            'courses.view',
            'courses.create',
            'courses.update',
            'courses.delete',

            // Module Management
            'modules.view',
            'modules.create',
            'modules.update',
            'modules.delete',

            // Quiz Management
            'quizzes.view',
            'quizzes.create',
            'quizzes.update',
            'quizzes.delete',

            // Reports
            'reports.view',
            'reports.export',

            // Analytics
            'analytics.view',

            // Roles & Permissions management
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            // Global course management (course_admin only — kept is_system)
            'courses.mark_global',
        ];

        foreach ($permissions as $name) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );

            // All platform-defined permissions are system-level.
            $permission->is_system = true;
            $permission->save();
        }

        // ── 2. Assign permissions to system roles ─────────────────────────────

        $rolePermissions = [
            'employer_admin' => [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'courses.view',
                'modules.view',
                'quizzes.view',
                'reports.view',
                'reports.export',
                'analytics.view',
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.delete',
            ],

            'hr_manager' => [
                'users.view',
                'users.create',
                'users.update',
                'courses.view',
                'modules.view',
                'quizzes.view',
                'reports.view',
                'reports.export',
            ],

            'department_manager' => [
                'users.view',
                'courses.view',
                'modules.view',
                'quizzes.view',
                'reports.view',
            ],

            'team_leader' => [
                'users.view',
                'courses.view',
                'modules.view',
                'quizzes.view',
            ],

            'employee' => [
                'courses.view',
                'modules.view',
                'quizzes.view',
            ],

            'vendor_admin' => [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'courses.view',
                'courses.create',
                'courses.update',
                'courses.delete',
                'modules.view',
                'modules.create',
                'modules.update',
                'modules.delete',
                'quizzes.view',
                'quizzes.create',
                'quizzes.update',
                'quizzes.delete',
                'reports.view',
                'reports.export',
                'analytics.view',
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.delete',
            ],

            'vendor_editor' => [
                'courses.view',
                'courses.create',
                'courses.update',
                'modules.view',
                'modules.create',
                'modules.update',
                'quizzes.view',
                'quizzes.create',
                'quizzes.update',
            ],

            'training_provider' => [
                'courses.view',
                'courses.create',
                'courses.update',
                'courses.delete',
                'modules.view',
                'modules.create',
                'modules.update',
                'modules.delete',
                'quizzes.view',
                'quizzes.create',
                'quizzes.update',
                'quizzes.delete',
            ],

            'course_admin' => [
                'courses.view',
                'courses.create',
                'courses.update',
                'courses.delete',
                'courses.mark_global',
                'modules.view',
                'modules.create',
                'modules.update',
                'modules.delete',
                'quizzes.view',
                'quizzes.create',
                'quizzes.update',
                'quizzes.delete',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->syncPermissions($perms);
            }
        }
    }
}
