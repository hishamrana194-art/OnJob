<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extend Spatie's roles and permissions tables with:
 *   - is_system  : marks built-in platform records that cannot be edited/deleted via API
 *   - employer_id: scopes a role to a specific employer (null = platform-wide)
 *   - vendor_id  : scopes a role to a specific vendor   (null = platform-wide)
 *
 * The existing unique(['name','guard_name']) constraint on `roles` is replaced by
 * a composite that includes both tenant keys, allowing different employers/vendors
 * to independently create roles with the same name.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── roles ─────────────────────────────────────────────────────────────
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('guard_name');
            $table->unsignedBigInteger('employer_id')->nullable()->after('is_system');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('employer_id');

            $table->foreign('employer_id')
                ->references('id')->on('employers')
                ->nullOnDelete();

            $table->foreign('vendor_id')
                ->references('id')->on('vendors')
                ->nullOnDelete();
        });

        // Drop the original globally-unique constraint and replace it with one
        // that allows the same name inside different tenants.
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name']);
            // Uniqueness is now: name + guard + employer + vendor
            // (NULL employer_id/vendor_id = system scope; DB treats each NULL as
            //  distinct in unique indexes, so two system roles with the same name
            //  are still prevented by the application-layer validation.)
            $table->index(['name', 'guard_name', 'employer_id', 'vendor_id'], 'roles_name_guard_tenant_index');
        });

        // ── permissions ───────────────────────────────────────────────────────
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('guard_name');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('roles_name_guard_tenant_index');
            $table->unique(['name', 'guard_name']); // restore Spatie default
            $table->dropForeign(['employer_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['is_system', 'employer_id', 'vendor_id']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};
