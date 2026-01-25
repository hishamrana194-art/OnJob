<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{public function up(): void
{
    Schema::create('user_quiz_attempts', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
        $table->foreignId('module_id')->constrained()->cascadeOnDelete();

        // always 1 (no retakes)
        $table->unsignedTinyInteger('attempt_number')->default(1);

        $table->unsignedInteger('score')->default(0);
        $table->json('answers')->nullable();

        $table->boolean('is_passed')->default(false);
        $table->unsignedInteger('time_taken_seconds')->default(0);

        $table->timestamps();

        // 🔒 Prevent duplicate attempts (VERY IMPORTANT)
        $table->unique(['user_id', 'quiz_id']);
    });
}


    public function down(): void
    {
        Schema::dropIfExists('user_quiz_attempts');
    }
};
