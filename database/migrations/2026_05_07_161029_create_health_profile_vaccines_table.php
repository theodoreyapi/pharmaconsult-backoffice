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
        Schema::create('profile_vaccinations', function (Blueprint $table) {
            $table->id('id_vaccination')->primary();

            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('vaccine_id')->nullable()
                ->comment('Lien vers le catalogue vaccins — nullable si hors catalogue');

            // Nom libre si le vaccin n'est pas dans le catalogue
            $table->string('vaccine_name_free', 150)->nullable()
                ->comment('Utilisé si vaccine_id est null');

            $table->date('vaccination_date');
            $table->date('next_reminder_date')->nullable();

            $table->enum('center_type', ['public', 'private'])->default('public');
            $table->string('center_name', 200)->nullable()
                ->comment('Ex: CHU de Cocody');

            // Image du certificat de vaccination
            $table->string('certificate_image', 500)->nullable()
                ->comment('Chemin relatif sur le serveur ex: certificates/2024/abc.jpg');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('profile_id')->references('id_profile')->on('health_profiles')->onDelete('cascade');
            $table->foreign('vaccine_id')->references('id_vaccine')->on('vaccines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_vaccinations');
        Schema::table('profile_vaccinations', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropColumn('profile_id');

            $table->dropForeign(['vaccine_id']);
            $table->dropColumn('vaccine_id');
        });
    }
};
