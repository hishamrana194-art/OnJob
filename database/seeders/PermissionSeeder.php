<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all permissions
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

            // Organizations
            'organizations.manage',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $rolePermissions = [
            'employer_t1' => $permissions, // Full access

            'employer_t2' => [
                'courses.view',
                'modules.view',
                'quizzes.view',
                'reports.view',
                'reports.export',
            ],

            'employer_t3' => [
                'courses.view',
                'modules.view',
            ],

            'employee' => [
                'courses.view',
                'modules.view',
                'quizzes.view',
            ],

            'vendor_t1' => [
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
                'organizations.manage',
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
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->syncPermissions($perms);
            }
        }
    }
}

