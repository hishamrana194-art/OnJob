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
        // For complete/fill-in-blank type questions
        Schema::create('question_blanks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->integer('blank_position'); // position of blank in question (1, 2, 3...)
            $table->json('correct_answer'); // translatable - the correct answer for this blank
            $table->json('acceptable_answers')->nullable(); // translatable - alternative accepted answers
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_blanks');
    }
};

