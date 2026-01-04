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
            [
                'name' => 'employer_t1',
                'guard_name' => 'web',
            ],
            [
                'name' => 'employer_t2',
                'guard_name' => 'web',
            ],
            [
                'name' => 'employer_t3',
                'guard_name' => 'web',
            ],
            [
                'name' => 'employee',
                'guard_name' => 'web',
            ],
            [
                'name' => 'vendor_t1',
                'guard_name' => 'web',
            ],
            [
                'name' => 'vendor_admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'training_provider',
                'guard_name' => 'web',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}

