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

        Schema::create('medicament_substituts', function (Blueprint $table) {
            $table->id('id_subtitut')->primary();

            $table->unsignedBigInteger('substitut_id');
            $table->foreign('substitut_id')->references('id_medicament')->on('medicaments');

            $table->unsignedBigInteger('medicament_id');
            $table->foreign('medicament_id')->references('id_medicament')->on('medicaments')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
        Schema::dropIfExists('medicament_substituts');
        Schema::table('medicament_substituts', function (Blueprint $table) {
            $table->dropForeign(['substitut_id', 'medicament_id']);
            $table->dropColumn('substitut_id');
            $table->dropColumn('medicament_id');
        });
    }
};
