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
        Schema::create('vaccines', function (Blueprint $table) {
            $table->id('id_vaccine')->primary();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('short_name', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('public_price', 10, 2)->default(0)->comment('0 = GRATUIT');
            $table->decimal('private_price_min', 10, 2)->nullable();
            $table->decimal('private_price_max', 10, 2)->nullable();
            $table->string('currency', 10)->default('FCFA');
            $table->text('important_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccines');
    }
};
