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
        Schema::create('vaccines', function (Blueprint $table) {
            $table->id('id_vaccine')->primary();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('short_name', 50)->nullable();
            $table->text('description')->nullable();

            // Tarification (Adaptée au contexte de Côte d'Ivoire INHP / Vétos privés)
            $table->decimal('public_price', 10, 2)->default(0)->comment('0 = GRATUIT');
            $table->decimal('private_price_min', 10, 2)->nullable();
            $table->decimal('private_price_max', 10, 2)->nullable();
            $table->string('currency', 10)->default('FCFA');

            // Typologie et Cible
            $table->enum('vaccine_type', ['human', 'animal'])->default('human');
            $table->string('target_species', 50)->nullable()->comment('Chien, Chat, Cheval, Toutes ou NULL si humain');
            $table->string('targeted_disease', 150)->nullable()->comment('Maladie(s) ciblée(s)');

            // Informations UI issues des fichiers
            $table->string('administration_mode', 100)->nullable()->comment('Sous-cutanée, intradermique...');
            $table->string('scientific_type', 150)->nullable()->comment('Vivant atténué, inactivé...');
            $table->text('protected_against')->nullable()->comment('Protège contre quoi ?');
            $table->text('target_public')->nullable()->comment('Public concerné / Description UI');

            $table->text('important_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('source_url')->nullable();
            $table->string('validation_status', 50)->default('pending')->comment('En attente, Validé DSV, Validé INHP');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccines');
    }
};
