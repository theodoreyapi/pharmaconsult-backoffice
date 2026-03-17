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
        Schema::create('rechargements', function (Blueprint $table) {
            $table->id('id_rechargement')->primary();

            $table->string('transaction_id')->nullable();
            $table->string('checkout_session_id')->nullable();

            $table->string('currency');
            $table->double('montant')->default(0);
            $table->string('payment_method'); // wave, orange, moov, mtn
            $table->string('status')->default('pending')->comment('pending', 'success', 'failed');

            $table->string('username');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rechargements');
    }
};
