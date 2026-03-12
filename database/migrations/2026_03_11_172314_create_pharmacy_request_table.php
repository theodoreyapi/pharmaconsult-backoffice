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
        Schema::create('pharmacy_request', function (Blueprint $table) {
            $table->id('id_pharmacy_request')->primary();

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->unsignedBigInteger('request_medicament_id')->nullable();
            $table->foreign('request_medicament_id')->references('id_request')->on('request_medicament')->onDelete('cascade');

            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_request');
        Schema::table('pharmacy_request', function (Blueprint $table) {
            $table->dropForeign(['medicament_id', 'pharmacy_id']);
            $table->dropColumn('medicament_id');
            $table->dropColumn('pharmacy_id');
        });
    }
};
