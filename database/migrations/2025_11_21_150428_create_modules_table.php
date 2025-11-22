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
         Schema::create('modules', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('vendor_id')->nullable();
        $table->string('title');
        $table->enum('type', ['video', 'pdf', 'quiz', 'pocket_guide']);
        $table->string('file_url')->nullable();
        $table->text('description')->nullable();
        $table->timestamps();

        $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
