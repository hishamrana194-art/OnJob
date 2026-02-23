<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_mandatory')->default(false)->after('status');
        });

        Schema::table('user_course_enrollments', function (Blueprint $table) {
            $table->timestamp('due_at')->nullable()->after('enrolled_at');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('is_mandatory');
        });
        Schema::table('user_course_enrollments', function (Blueprint $table) {
            $table->dropColumn('due_at');
        });
    }
};
