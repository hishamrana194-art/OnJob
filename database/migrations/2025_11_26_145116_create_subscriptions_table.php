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
    Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->morphs('subscribable'); // employer OR vendor
    $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
    $table->enum('status', ['active', 'expired', 'suspended']);
    $table->integer('seats_limit');
    $table->enum('billing_period', ['monthly', 'yearly']);
    $table->decimal('price_per_period', 8, 2);
    $table->date('started_at');
    $table->date('ends_at');
    $table->timestamps();
});

    }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
