<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->enum('user_type', ['employer_user', 'vendor_user', 'training_provider'])
                ->default('employer_user')
                ->after('avatar');

            $table->unsignedBigInteger('employer_id')->nullable()->after('user_type');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('employer_id');
        });

        // Drop old organization_id foreign key and column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'organization_id')) {
                $table->dropForeign(['organization_id']);
                $table->dropIndex(['organization_id']);
                $table->dropColumn('organization_id');
            }
        });

        // Update role enum with new values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'employer_admin',
            'hr_manager',
            'department_manager',
            'team_leader',
            'employee',
            'vendor_admin',
            'vendor_editor',
            'training_provider'
        ) DEFAULT 'employee'");

        // Add foreign keys
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('employer_id')
                ->references('id')
                ->on('employers')
                ->onDelete('cascade');

            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->onDelete('cascade');

            // Add indexes
            $table->index('employer_id');
            $table->index('vendor_id');
            $table->index('user_type');
            $table->index(['user_type', 'employer_id'], 'users_type_employer_idx');
            $table->index(['user_type', 'vendor_id'], 'users_type_vendor_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['employer_id']);
            $table->dropForeign(['vendor_id']);

            // Drop indexes
            $table->dropIndex(['employer_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['user_type']);
            $table->dropIndex('users_type_employer_idx');
            $table->dropIndex('users_type_vendor_idx');

            // Drop columns
            $table->dropColumn(['user_type', 'employer_id', 'vendor_id']);
        });

        // Restore organization_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('avatar')->constrained('vendors')->nullOnDelete();
            $table->index('organization_id');
        });

        // Restore old role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'employer_t1',
            'employer_t2',
            'employer_t3',
            'employee',
            'vendor_t1',
            'vendor_admin',
            'training_provider'
        ) DEFAULT 'employee'");
    }
};

