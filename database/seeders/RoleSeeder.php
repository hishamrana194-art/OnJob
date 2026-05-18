<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * System roles — built-in platform roles that may not be edited or deleted
     * via the API. The `is_system` flag is set directly (bypassing $fillable)
     * because it must never be settable through user-supplied payloads.
     */
    public function run(): void
    {
        $roles = [
            // Employer roles
            ['name' => 'employer_admin',      'guard_name' => 'web'],
            ['name' => 'hr_manager',           'guard_name' => 'web'],
            ['name' => 'department_manager',   'guard_name' => 'web'],
            ['name' => 'team_leader',          'guard_name' => 'web'],
            ['name' => 'employee',             'guard_name' => 'web'],
            // Vendor roles
            ['name' => 'vendor_admin',         'guard_name' => 'web'],
            ['name' => 'vendor_editor',        'guard_name' => 'web'],
            // Training Provider
            ['name' => 'training_provider',    'guard_name' => 'web'],
            // Platform-level
            ['name' => 'course_admin',         'guard_name' => 'web'],
        ];

        foreach ($roles as $data) {
            $role = Role::firstOrCreate(
                ['name' => $data['name'], 'guard_name' => $data['guard_name']]
            );

            // Mark as system — direct assignment bypasses $fillable intentionally.
            // Seeders are trusted code paths; API clients can never reach this.
            $role->is_system = true;
            $role->save();
        }
    }
}
