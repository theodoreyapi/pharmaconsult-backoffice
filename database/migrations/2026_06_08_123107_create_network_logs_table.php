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
        Schema::create('network_logs', function (Blueprint $table) {
            $table->id('id_network_log')->primary();
            $table->string('action')->comment('Dossier consulté, Mesure ajoutée, Traitement renouvelé...');

            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id_patient')->on('patients');

            $table->unsignedBigInteger('pharmacy_id')->comment('pharmacie qui a agi');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy');

            $table->unsignedBigInteger('pharmacien_id');
            $table->foreign('pharmacien_id')->references('id_pharmacien')->on('pharmacien');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_logs');
        Schema::table('network_logs', function (Blueprint $table) {
            $table->dropForeign(['patient_id', 'pharmacy_id', 'pharmacien_id']);
            $table->dropColumn('patient_id');
            $table->dropColumn('pharmacy_id');
            $table->dropColumn('pharmacien_id');
        });
    }
};
