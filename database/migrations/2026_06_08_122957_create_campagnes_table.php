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
        Schema::create('campagnes', function (Blueprint $table) {
            $table->id('id_campagne')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['BROUILLON', 'PLANIFIE', 'EN_COURS', 'TERMINE'])->default('BROUILLON');
            $table->enum('portee', ['MA_PHARMACIE', 'REGIONAL', 'NATIONAL'])->default('MA_PHARMACIE');
            $table->enum('channel', ['WHATSAPP', 'SMS'])->default('WHATSAPP');
            $table->string('message_template')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('patients_count')->default(0);

            $table->unsignedBigInteger('pathologie_id')->nullable();
            $table->foreign('pathologie_id')->references('id_pathologie')->on('pathologies');

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id_pharmacien')->on('pharmacien');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagnes');
        Schema::table('campagnes', function (Blueprint $table) {
            $table->dropForeign(['pathologie_id', 'pharmacy_id', 'created_by']);
            $table->dropColumn('pathologie_id');
            $table->dropColumn('pharmacy_id');
            $table->dropColumn('created_by');
        });
    }
};
