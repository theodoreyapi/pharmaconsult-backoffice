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
        Schema::create('vaccine_equivalents', function (Blueprint $table) {
            $table->id('id_equivalent')->primary();

            $table->unsignedBigInteger('vaccine_id');

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('vaccine_id')->references('id_vaccine')->on('vaccines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_equivalents');
        Schema::table('vaccine_equivalents', function (Blueprint $table) {
            $table->dropForeign(['vaccine_id']);
            $table->dropColumn('vaccine_id');
        });
    }
};
