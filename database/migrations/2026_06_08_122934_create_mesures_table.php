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
        Schema::create('mesures', function (Blueprint $table) {
            $table->id('id_mesure')->primary();
            $table->enum('type', ['PRESSION_ARTERIELLE', 'FREQUENCE_CARDIAQUE', 'GLYCEMIE', 'POIDS_IMC']);

            // Pression artérielle
            $table->integer('systolic')->nullable();
            $table->integer('diastolic')->nullable();

            // Fréquence cardiaque
            $table->integer('heart_rate_bpm')->nullable();

            // Glycémie
            $table->decimal('glycemia_mmol', 5, 2)->nullable();
            $table->boolean('is_fasting')->default(true);

            // Poids / IMC
            $table->decimal('weight_kg', 5, 1)->nullable();
            $table->decimal('height_cm', 5, 1)->nullable();
            $table->decimal('imc', 4, 1)->nullable();

            $table->text('comment')->nullable();
            $table->string('status_label')->nullable()->comment('Critique, Élevé, Normal, Surpoids');

            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id_patient')->on('patients')->onDelete('cascade');

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
        Schema::dropIfExists('mesures');
        Schema::table('mesures', function (Blueprint $table) {
            $table->dropForeign(['patient_id', 'pharmacien_id']);
            $table->dropColumn('patient_id');
            $table->dropColumn('pharmacien_id');
        });
    }
};
