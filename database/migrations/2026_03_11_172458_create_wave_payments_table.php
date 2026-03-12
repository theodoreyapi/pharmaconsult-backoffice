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
        Schema::create('wave_payments', function (Blueprint $table) {
            $table->id('id_wave')->primary();
            $table->string('aggregated_merchant_id')->nullable();
            $table->string('amount');
            $table->string('business_name')->nullable();
            $table->bigInteger('checkout_id')->unique();
            $table->string('checkout_status')->nullable();
            $table->string('client_reference')->nullable();
            $table->string('currency');
            $table->string('error_url')->nullable();
            $table->string('last_payment_error')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('success_url')->nullable();
            $table->string('transaction_hash')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('wave_launch_url')->nullable();
            $table->dateTime('when_completed')->nullable();
            $table->dateTime('when_created')->nullable();
            $table->dateTime('when_expires')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wave_payments');
    }
};
