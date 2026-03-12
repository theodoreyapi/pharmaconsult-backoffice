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
        Schema::create('modules', function (Blueprint $table) {
            $table->id('id_module')->primary();
            $table->string('description')->nullable();
            $table->string('libelle')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id('id_service')->primary();
            $table->string('description')->nullable();
            $table->integer('duration');
            $table->string('libelle')->nullable();
            $table->double('price')->default(0);

            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id_module')->on('modules')->onDelete('cascade');

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
        Schema::dropIfExists('services');
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });
    }
};
