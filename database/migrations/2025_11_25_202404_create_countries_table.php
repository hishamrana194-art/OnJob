<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('countries', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // اسم الدولة
        $table->string('code')->nullable(); // كود الدولة مثل EG, US
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
