<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make vendor_id nullable (global courses have no vendor)
     * and add the is_global flag.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 1) Drop the existing non-null FK so we can change the column.
            $table->dropForeign(['vendor_id']);

            // 2) Allow NULL — global courses are not tied to any vendor.
            $table->unsignedBigInteger('vendor_id')->nullable()->change();

            // 3) Re-add the FK. Use nullOnDelete so deleting a vendor
            //    detaches its courses rather than cascading deletes.
            $table->foreign('vendor_id')
                  ->references('id')
                  ->on('vendors')
                  ->nullOnDelete();

            // 4) The flag that makes a course universally visible.
            //    Constraint: is_global = true  <=>  vendor_id IS NULL
            $table->boolean('is_global')->default(false)->after('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('is_global');

            // Restore original NOT NULL FK with cascade delete
            $table->unsignedBigInteger('vendor_id')->nullable(false)->change();
            $table->foreign('vendor_id')
                  ->references('id')
                  ->on('vendors')
                  ->cascadeOnDelete();
        });
    }
};
