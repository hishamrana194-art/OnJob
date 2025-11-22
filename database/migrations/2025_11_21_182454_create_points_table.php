<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');   // لازم العمود يكون موجود قبل الـ foreign key
            $table->unsignedBigInteger('reward_id'); // لازم العمود يكون موجود قبل الـ foreign key
            $table->integer('points');
            $table->timestamps();
        });

        // إضافة الـ foreign keys بعد إنشاء الجدول
        /*Schema::table('points', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
        });*/
    }

    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
