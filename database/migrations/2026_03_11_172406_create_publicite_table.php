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
        Schema::create('publicite', function (Blueprint $table) {
            $table->id('id_publicite')->primary();
            $table->dateTime('end_date')->nullable();
            $table->string('image')->nullable();
            $table->string('lien')->nullable();
            $table->string('name')->nullable();
            $table->double('price')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicite');
    }
};
