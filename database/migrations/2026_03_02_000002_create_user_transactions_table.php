<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // xp_earn | coins_earn | coins_spend
            $table->unsignedBigInteger('amount');
            $table->string('reason')->nullable();
            $table->string('ref_type')->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->timestamps();

            // Prevent duplicate rewards for the same ref
            $table->unique(['user_id', 'type', 'ref_type', 'ref_id'], 'uniq_user_txn_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_transactions');
    }
};
