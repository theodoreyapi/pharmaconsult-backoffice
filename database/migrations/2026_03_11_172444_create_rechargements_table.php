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
            $table->string('channels')->nullable();
            $table->string('client_transaction_id')->nullable();
            $table->string('code')->nullable();
            $table->string('currency')->nullable();
            $table->string('description')->nullable();
            $table->string('id_transaction')->nullable();
            $table->string('message')->nullable();
            $table->double('montant')->nullable();
            $table->string('notify_url')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('phone')->nullable();
            $table->string('prefix')->nullable();
            $table->string('status')->nullable();
            $table->string('treatment_status')->nullable();
            $table->string('username')->nullable();
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
