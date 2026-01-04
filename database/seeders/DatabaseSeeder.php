<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core data
            AdminSeeder::class,
            CountriesSeeder::class,

            // RBAC - Roles & Permissions
            RoleSeeder::class,
            PermissionSeeder::class,

            // Business entities
            VendorSeeder::class,
            EmployerSeeder::class,
            PackageSeeder::class,
            SubscriptionSeeder::class,

            // Users
            UserSeeder::class,

            // LMS data
            CategorySeeder::class,
            CourseSeeder::class,
            ModuleSeeder::class,
            ModuleItemSeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
