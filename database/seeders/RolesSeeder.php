<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $roles = [
        'admin',
        'employer_t1',
        'employer_t2',
        'employer_t3',
        'employee',
        'vendor',
        'provider'
    ];

    foreach ($roles as $role) {
        \Spatie\Permission\Models\Role::create(['name' => $role]);
    }
    }
}
