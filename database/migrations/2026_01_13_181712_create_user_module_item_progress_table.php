<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_module_item_progress', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('module_id')->constrained()->cascadeOnDelete();
    $table->foreignId('module_item_id')->constrained()->cascadeOnDelete();

    $table->boolean('is_completed')->default(false);
    $table->unsignedInteger('watched_duration_seconds')->default(0);

    $table->timestamps();

    // indexes & constraints
    $table->unique(['user_id', 'module_item_id']);
    $table->index(['user_id', 'module_id']);
});

        
    }

    public function down(): void
    {
        Schema::dropIfExists('user_module_item_progress');
    }
};
