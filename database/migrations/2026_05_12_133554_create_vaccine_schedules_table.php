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

            /*
    |--------------------------------------------------------------------------
    | ÂGE
    |--------------------------------------------------------------------------
    */

            $table->integer('min_age_months')->default(0);
            $table->integer('max_age_months')->nullable();
            $table->string('age_label')->nullable();

            /*
    |--------------------------------------------------------------------------
    | SEXE
    |--------------------------------------------------------------------------
    */

            $table->enum('gender', [
                'all',
                'masculin',
                'feminin'
            ])->default('all');

            /*
    |--------------------------------------------------------------------------
    | GROSSESSE
    |--------------------------------------------------------------------------
    */

            $table->boolean('only_pregnant')->default(false);

            /*
    |--------------------------------------------------------------------------
    | VOYAGE
    |--------------------------------------------------------------------------
    */

            $table->boolean('for_travelers')->default(false);
            $table->string('travel_zone')->nullable();
            // Afrique, Asie, Amazonie, Mecque...

            /*
    |--------------------------------------------------------------------------
    | CONDITIONS SPÉCIALES
    |--------------------------------------------------------------------------
    */

            $table->boolean('for_health_workers')->default(false);
            $table->boolean('for_immunocompromised')->default(false);
            $table->boolean('for_seniors')->default(false);

            /*
    |--------------------------------------------------------------------------
    | RAPPELS
    |--------------------------------------------------------------------------
    */

            $table->boolean('is_booster')->default(false);
            $table->integer('booster_every_months')->nullable();

            /*
    |--------------------------------------------------------------------------
    | INFOS
    |--------------------------------------------------------------------------
    */

            $table->integer('dose_number')->nullable();
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
