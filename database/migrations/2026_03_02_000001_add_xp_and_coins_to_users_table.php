<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'xp')) {
                $table->unsignedBigInteger('xp')->default(0)->after('points');
            }
            if (!Schema::hasColumn('users', 'coins')) {
                $table->unsignedBigInteger('coins')->default(0)->after('xp');
            }
        });

        // One-time backfill: if legacy `points` existed, treat it as initial XP.
        // Safe to run multiple times.
        if (Schema::hasColumn('users', 'points') && Schema::hasColumn('users', 'xp')) {
            DB::statement('UPDATE users SET xp = points WHERE xp = 0 AND points > 0');
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'coins')) {
                $table->dropColumn('coins');
            }
            if (Schema::hasColumn('users', 'xp')) {
                $table->dropColumn('xp');
            }
        });
    }
};
