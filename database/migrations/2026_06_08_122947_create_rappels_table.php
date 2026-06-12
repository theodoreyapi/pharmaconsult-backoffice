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
        Schema::create('rappels', function (Blueprint $table) {
            $table->id('id_rappel')->primary();
            $table->enum('channel', ['WHATSAPP', 'SMS']);
            $table->enum('type', ['RENOUVELLEMENT', 'MESURE', 'CONSEIL', 'CAMPAGNE', 'PERSONNALISE'])->default('RENOUVELLEMENT');
            $table->text('message');
            $table->enum('status', ['ENVOYE', 'LIVRE', 'LU', 'ECHEC'])->default('ENVOYE');
            $table->timestamp('sent_at')->nullable();

            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id_patient')->on('patients')->onDelete('cascade');

            $table->unsignedBigInteger('pharmacien_id');
            $table->foreign('pharmacien_id')->references('id_pharmacien')->on('pharmacien');

            $table->unsignedBigInteger('message_id')->nullable();
            $table->foreign('message_id')->references('id_message')->on('messages')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rappels');
        Schema::table('rappels', function (Blueprint $table) {
            $table->dropForeign(['patient_id', 'pharmacien_id', 'message_id']);
            $table->dropColumn('patient_id');
            $table->dropColumn('pharmacien_id');
            $table->dropColumn('message_id');
        });
    }
};
