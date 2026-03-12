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
        Schema::create('reservation_medicament', function (Blueprint $table) {
            $table->id('id_reservation')->primary();
            $table->dateTime('date_expiration')->nullable();
            $table->dateTime('date_reservation')->nullable();

            $table->unsignedBigInteger('medicament_id');
            $table->foreign('medicament_id')->references('id_medicament')->on('medicaments')->onDelete('cascade');

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->string('status')->nullable();
            $table->string('user_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_medicament');
        Schema::table('reservation_medicament', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id', 'medicament_id']);
            $table->dropColumn('pharmacy_id');
            $table->dropColumn('medicament_id');
        });
    }
};
