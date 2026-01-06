<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration sets default user_type for existing users based on their role.
     * Note: organization_id column was already removed in previous migration.
     */
    public function up(): void
    {
        // Update users based on their role
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $userType = 'employer_user'; // default

            // Check if the role indicates a vendor user
            if (in_array($user->role, ['vendor_admin', 'vendor_editor', 'vendor_t1'])) {
                $userType = 'vendor_user';
            } elseif ($user->role === 'training_provider') {
                $userType = 'training_provider';
            }

            // Update the user
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'user_type' => $userType,
                ]);
        }

        // Ensure all users have a user_type set
        DB::table('users')
            ->whereNull('user_type')
            ->update(['user_type' => 'employer_user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all users to default user_type (cannot be null due to NOT NULL constraint)
        DB::table('users')->update([
            'user_type' => 'employer_user',
        ]);
    }
};
