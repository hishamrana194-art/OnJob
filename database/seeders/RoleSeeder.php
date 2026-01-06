<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // Employer roles
            ['name' => 'employer_admin', 'guard_name' => 'web'],
            ['name' => 'hr_manager', 'guard_name' => 'web'],
            ['name' => 'department_manager', 'guard_name' => 'web'],
            ['name' => 'team_leader', 'guard_name' => 'web'],
            ['name' => 'employee', 'guard_name' => 'web'],
            // Vendor roles
            ['name' => 'vendor_admin', 'guard_name' => 'web'],
            ['name' => 'vendor_editor', 'guard_name' => 'web'],
            // Training Provider
            ['name' => 'training_provider', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}

