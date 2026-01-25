<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('role', [
                'employer_t1',
                'employer_t2',
                'employer_t3',
                'employee',
                'vendor_t1',
                'vendor_admin',
                'training_provider'
            ])->default('employee');
            $table->integer('points')->default(0)->after('role');
            $table->string('locale', 5)->default('en')->after('points');
            $table->boolean('is_active')->default(true)->after('locale');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            // Add indexes

            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);

            $table->dropColumn([
                'phone',
                'avatar',
                'role',
                'points',
                'locale',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};

