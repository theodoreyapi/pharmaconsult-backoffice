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
        Schema::create('medicaments', function (Blueprint $table) {
            $table->id('id_medicament')->primary();
            $table->string('code_cip')->nullable();
            $table->string('name')->nullable();
            $table->string('notice')->nullable();
            $table->string('price')->nullable();
            $table->string('principe_actif')->nullable();
            $table->string('medicament_picture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};
