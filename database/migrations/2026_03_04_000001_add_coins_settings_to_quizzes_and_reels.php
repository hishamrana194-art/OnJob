<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Admin-configurable: how many coins user earns for passing this quiz.
            // NULL means use the GamificationService default (COINS_QUIZ_PASS = 10).
            $table->unsignedSmallInteger('coins_on_pass')->nullable()->after('passing_score')
                ->comment('Coins awarded when user passes this quiz. NULL = system default.');
            // XP on pass (also configurable per-quiz)
            $table->unsignedSmallInteger('xp_on_pass')->nullable()->after('coins_on_pass')
                ->comment('XP awarded when user passes this quiz. NULL = system default.');
        });

        Schema::table('reels', function (Blueprint $table) {
            // Admin-configurable: coins & XP for finishing a reel.
            $table->unsignedSmallInteger('coins_on_complete')->nullable()->after('order')
                ->comment('Coins awarded when user completes this reel. NULL = system default.');
            $table->unsignedSmallInteger('xp_on_complete')->nullable()->after('coins_on_complete')
                ->comment('XP awarded when user completes this reel. NULL = system default.');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['coins_on_pass', 'xp_on_pass']);
        });

        Schema::table('reels', function (Blueprint $table) {
            $table->dropColumn(['coins_on_complete', 'xp_on_complete']);
        });
    }
};
