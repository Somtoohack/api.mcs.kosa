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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('settlement', 10, 2);
            $table->decimal('charge', 10, 2);
            $table->string('first_name')->nullable();
            $table->string('surname')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('email')->nullable();
            $table->string('bvn')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('payer_account_name')->nullable();
            $table->string('payer_account_no')->nullable();
            $table->string('payer_bank_name')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('date')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};