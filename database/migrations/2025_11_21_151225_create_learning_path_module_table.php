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
    {  Schema::create('learning_path_module', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('learning_path_id');
        $table->unsignedBigInteger('module_id');
        $table->integer('order')->default(1);

        $table->foreign('learning_path_id')->references('id')->on('learning_paths')->onDelete('cascade');
        $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_path_module');
    }
};
