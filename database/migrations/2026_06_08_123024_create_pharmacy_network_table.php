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
        Schema::create('pharmacy_network', function (Blueprint $table) {
            $table->id('id_pharmacy_network')->primary();

            $table->unsignedBigInteger('pharmacy_id')->comment('pharmacie principale');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->unsignedBigInteger('partner_pharmacy_id')->comment('pharmacie partenaire');
            $table->foreign('partner_pharmacy_id')->references('id_pharmacy')->on('pharmacy');

            $table->enum('type', ['PRINCIPALE', 'SECONDAIRE'])->default('SECONDAIRE');
            $table->string('status')->default('ACTIF');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_network');
    }
};
