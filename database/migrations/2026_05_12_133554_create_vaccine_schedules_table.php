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
        Schema::create('vaccine_schedules', function (Blueprint $table) {
            $table->id('id_schedule')->primary();
            $table->unsignedBigInteger('vaccine_id');

            // Étape de la dose
            $table->string('phase_name')->nullable()->comment('Dose 1, Primovaccination, Rappel...');
            $table->integer('dose_number')->nullable();

            // Critères d'âge
            $table->decimal('min_age_months', 5, 1)->default(0); // decimal pour gérer les demi-mois (ex: 1.5 mois pour DTCP)
            $table->decimal('max_age_months', 5, 1)->nullable();
            $table->string('age_label')->nullable();

            // Profils physiologiques / Humains
            $table->enum('gender', ['all', 'masculin', 'feminin'])->default('all');
            $table->boolean('only_pregnant')->default(false);

            // Contextes environnementaux / Comportementaux (Valable humains & animaux)
            $table->boolean('for_travelers')->default(false);
            $table->string('travel_zone')->nullable();
            $table->boolean('in_community')->default(false)->comment('Crèche/Milieu scolaire ou Pension/Refuge/Écurie');

            // Risques spécifiques santé
            $table->boolean('for_health_workers')->default(false);
            $table->boolean('for_immunocompromised')->default(false);
            $table->boolean('for_seniors')->default(false);
            $table->boolean('exposed_to_vectors')->default(false)->comment('Exposition moustiques (West Nile, etc.) ou Rage/Post-exposition');

            // Logique de Rappel
            $table->boolean('is_booster')->default(false);
            $table->integer('booster_every_months')->nullable();

            // Meta
            $table->text('important_note')->nullable();
            $table->integer('priority')->default(0);

            $table->timestamps();

            $table->foreign('vaccine_id')->references('id_vaccine')->on('vaccines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_schedules');
        Schema::table('vaccine_schedules', function (Blueprint $table) {
            $table->dropForeign(['vaccine_id']);
            $table->dropColumn('vaccine_id');
        });
    }
};
