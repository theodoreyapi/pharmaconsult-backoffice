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
        Schema::create('vaccine_restrictions', function (Blueprint $table) {
            $table->id('id_restriction')->primary();

            $table->unsignedBigInteger('vaccine_id');

            $table->enum('restriction_type', [
                'pregnancy',
                'immunocompromised',
                'allergy',
            ]);

            $table->text('reason')->nullable();

            $table->timestamps();

            $table->foreign('vaccine_id')->references('id_vaccine')->on('vaccines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_restrictions');
        Schema::table('vaccine_restrictions', function (Blueprint $table) {
            $table->dropForeign(['vaccine_id']);
            $table->dropColumn('vaccine_id');
        });
    }
};
