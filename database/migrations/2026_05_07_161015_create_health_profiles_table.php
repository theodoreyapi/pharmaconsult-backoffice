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
        Schema::create('health_profiles', function (Blueprint $table) {
            $table->id('id_profile')->primary();
            $table->unsignedBigInteger('user_id');

             $table->string('name', 150);
            $table->enum('profile_type', ['human', 'animal'])->default('human');

            // Champs humain
            $table->string('relation', 100)->nullable()
                  ->comment('Moi-même, Parent, Enfant, Frère, Sœur, Conjoint, Ami…');

            // Champs animal
            $table->string('animal_type', 100)->nullable()
                  ->comment('Chien, Chat, Cheval, Lapin, Oiseau…');

            $table->enum('gender', ['masculin', 'feminin'])->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_frequent_traveler')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users_pharma')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_profiles');
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
