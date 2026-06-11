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
        Schema::create('pathologies', function (Blueprint $table) {
            $table->id('id_pathologie')->primary();
            $table->string('code')->comment('HTA, DIAB, ASTH, DYSLIP');
            $table->string('name')->comment('Hypertension artérielle, Diabète, Asthme, Dyslipidémie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathologies');
    }
};
