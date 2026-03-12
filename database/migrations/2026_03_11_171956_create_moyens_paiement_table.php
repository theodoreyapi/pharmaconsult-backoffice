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
        Schema::create('moyens_paiement', function (Blueprint $table) {
            $table->id('id_moyen_payment')->primary();
            $table->string('description')->nullable();
            $table->string('name')->nullable();
            $table->string('payment_method_picture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moyens_paiement');
    }
};
