<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Employer permissions
            'manage-users',
            'create-course',
            'assign-learning-path',
            'view-analytics',
            'send-notification',

            // Employee permissions
            'take-course',
            'collect-points',

            // Vendor permissions
            'vendor-create-course',
            'vendor-send-notification',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [
            'admin',
            'employer_t1',
            'employer_t2',
            'employer_t3',
            'vendor',
            'employee'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions to Roles
        |--------------------------------------------------------------------------
        */

        // Admin gets everything
        Role::findByName('admin')->givePermissionTo(Permission::all());

        // Employer T1
        Role::findByName('employer_t1')->givePermissionTo([
            'manage-users',
            'create-course',
            'assign-learning-path',
            'view-analytics',
            'send-notification'
        ]);

        // Employer T2
        Role::findByName('employer_t2')->givePermissionTo([
            'create-course',
            'assign-learning-path',
            'view-analytics',
        ]);

        // Employer T3
        Role::findByName('employer_t3')->givePermissionTo([
            'assign-learning-path',
            'view-analytics'
        ]);

        // Vendor
        Role::findByName('vendor')->givePermissionTo([
            'vendor-create-course',
            'vendor-send-notification'
        ]);

        // Employee
        Role::findByName('employee')->givePermissionTo([
            'take-course',
            'collect-points'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Default Admin
        |--------------------------------------------------------------------------
        */

        if (!User::where('email', 'admin@onjob.com')->exists()) {
            $admin = User::create([
                'name' => 'OnJob Admin',
                'email' => 'admin@onjob.com',
                'password' => Hash::make('Admin12345'),
            ]);

            $admin->assignRole('admin');
        }
    }
}
