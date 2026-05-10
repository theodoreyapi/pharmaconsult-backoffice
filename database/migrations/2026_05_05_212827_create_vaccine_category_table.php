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
        Schema::create('vaccine_category', function (Blueprint $table) {
            $table->id('id_vaccine_category')->primary();

            $table->unsignedBigInteger('vaccine_id')->nullable();
            $table->foreign('vaccine_id')->references('id_vaccine')->on('vaccines')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id_categorie')->on('categories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_category');
        Schema::table('vaccine_category', function (Blueprint $table) {
            $table->dropForeign(['vaccine_id', 'category_id']);
            $table->dropColumn('vaccine_id');
            $table->dropColumn('category_id');
        });
    }
};
