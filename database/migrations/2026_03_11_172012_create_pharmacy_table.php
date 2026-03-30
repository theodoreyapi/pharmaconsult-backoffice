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
        Schema::create('pharmacy', function (Blueprint $table) {
            $table->id('id_pharmacy')->primary();
            $table->string('address')->nullable();
            $table->dateTime('end_garde_date')->nullable();
            $table->string('facade_image')->nullable();
            $table->string('gps_coordinates')->nullable();
            $table->string('name')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->dateTime('start_garde_date')->nullable();

            $table->unsignedBigInteger('commune_id');
            $table->foreign('commune_id')->references('id_commune')->on('commune');

            $table->string('whats_app_phone_number')->nullable();
            $table->string('closing_hours')->nullable();
            $table->timestamps();
        });

        Schema::create('review', function (Blueprint $table) {
            $table->id('id_review')->primary();
            $table->string('commentaire')->nullable();
            $table->integer('evaluation');
            $table->string('username');

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('pharmacy_assurances', function (Blueprint $table) {
            $table->id('id_pharmacy_assurance')->primary();

            $table->unsignedBigInteger('assurance_id');
            $table->foreign('assurance_id')->references('id_assurance')->on('assurances');

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('pharmacy_payment_methods', function (Blueprint $table) {
            $table->id('id_pharmacy_payment_method')->primary();

            $table->unsignedBigInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id_moyen_payment')->on('moyens_paiement');

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy');
        Schema::dropIfExists('pharmacy_payment_methods');
        Schema::dropIfExists('pharmacy_assurances');
        Schema::dropIfExists('review');
        Schema::table('pharmacy', function (Blueprint $table) {
            $table->dropForeign(['commune_id']);
            $table->dropColumn('commune_id');
        });
        Schema::table('pharmacy_payment_methods', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id','payment_method_id']);
            $table->dropColumn('pharmacy_id');
            $table->dropColumn('payment_method_id');
        });
        Schema::table('pharmacy_assurances', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id','assurance_id']);
            $table->dropColumn('pharmacy_id');
            $table->dropColumn('assurance_id');
        });
        Schema::table('review', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id']);
            $table->dropColumn('pharmacy_id');
        });
    }
};
