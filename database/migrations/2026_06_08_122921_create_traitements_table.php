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
        Schema::create('traitements', function (Blueprint $table) {
            $table->id('id_traitement')->primary();
            $table->string('medication_name');
            $table->string('dosage')->nullable()->comment('ex: 5 mg/j');
            $table->integer('frequency_per_day')->default(1);
            $table->integer('quantity_delivered')->default(30);
            $table->integer('duration_days')->default(30);
            $table->date('dispensed_at');
            $table->date('estimated_end_date')->nullable();
            $table->string('status')->default('ACTIF')->comment('ACTIF, RENOUVELÉ, TERMINE');

            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id_patient')->on('patients')->onDelete('cascade');

            $table->unsignedBigInteger('pathologie_id')->nullable();
            $table->foreign('pathologie_id')->references('id_pathologie')->on('pathologies');

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
        Schema::dropIfExists('traitements');
        Schema::table('traitements', function (Blueprint $table) {
            $table->dropForeign(['patient_id', 'pathologie_id', 'pharmacien_id']);
            $table->dropColumn('patient_id');
            $table->dropColumn('pathologie_id');
            $table->dropColumn('pharmacien_id');
        });
    }
};
