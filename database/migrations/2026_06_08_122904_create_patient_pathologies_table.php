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
        Schema::create('patient_pathologies', function (Blueprint $table) {
            $table->id('id_patient_pathologie')->primary();

            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id_patient')->on('patients')->onDelete('cascade');

            $table->unsignedBigInteger('pathologie_id');
            $table->foreign('pathologie_id')->references('id_pathologie')->on('pathologies');

            $table->enum('priority', ['FAIBLE', 'MOYENNE', 'HAUTE', 'ELEVEE'])->default('MOYENNE');
            $table->enum('status', ['ACTIF', 'INACTIF'])->default('ACTIF');
            $table->date('start_date')->nullable();
            $table->string('doctor_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_pathologies');
        Schema::table('patient_pathologies', function (Blueprint $table) {
            $table->dropForeign(['patient_id','pathologie_id']);
            $table->dropColumn('patient_id');
            $table->dropColumn('pathologie_id');
        });
    }
};
