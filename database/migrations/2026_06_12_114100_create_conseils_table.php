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
        Schema::create('conseils', function (Blueprint $table) {
            $table->id('id_conseil')->primary();
            $table->string('type', 100)->comment('Conseil, Article, Astuce, etc.');
            $table->string('categorie',150)->comment('Hypertension, Diabète, Bien-être, Observance, etc.');
            $table->string('titre');
            $table->longText('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conseils');
    }
};
