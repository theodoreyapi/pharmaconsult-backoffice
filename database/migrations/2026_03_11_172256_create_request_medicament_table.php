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
        Schema::create('request_medicament', function (Blueprint $table) {
            $table->id('id_request')->primary();

            $table->unsignedBigInteger('medicament_id')->nullable();
            $table->foreign('medicament_id')->references('id_medicament')->on('medicaments')->onDelete('cascade');

            $table->string('status')->nullable();
            $table->string('username')->nullable();
            $table->string('comment')->nullable();
            $table->string('medicament_name')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_medicament');
        Schema::table('request_medicament', function (Blueprint $table) {
            $table->dropForeign(['medicament_id']);
            $table->dropColumn('medicament_id');
        });
    }
};
