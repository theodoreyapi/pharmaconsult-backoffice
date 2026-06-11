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
        Schema::create('patients', function (Blueprint $table) {
            $table->id('id_patient')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->unique();
            $table->string('email')->nullable();
            $table->enum('gender', ['FEMME', 'HOMME', 'AUTRE'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('city')->nullable();
            $table->string('commune')->nullable();
            $table->decimal('height_cm', 5, 1)->nullable();
            $table->string('qr_code')->comment('numero social CMU');
            $table->string('status')->default('EN_RETARD')->comment('A_JOUR, EN_RETARD, CRITIQUE, BIENTOT_RETARD');
            $table->string('active')->default('ACTIVE');

            // Consentements
            $table->boolean('consent_suivi')->default(false);
            $table->boolean('consent_whatsapp')->default(false);
            $table->boolean('consent_sms')->default(false);
            $table->boolean('consent_reseau')->default(false);

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->unsignedBigInteger('created_by')->comment('id_pharmacien');
            $table->foreign('created_by')->references('id_pharmacien')->on('pharmacien');

            $table->unsignedBigInteger('user_id')->comment('utilisateur qui utilise l\'application')->nullable();
            $table->foreign('user_id')->references('id_user')->on('users_pharma');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id','created_by','user_id']);
            $table->dropColumn('pharmacy_id');
            $table->dropColumn('created_by');
            $table->dropColumn('user_id');
        });
    }
};
