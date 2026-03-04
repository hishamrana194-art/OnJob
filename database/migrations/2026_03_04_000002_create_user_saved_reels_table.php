<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_saved_reels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reel_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Each user can save a reel only once
            $table->unique(['user_id', 'reel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_saved_reels');
    }
};
