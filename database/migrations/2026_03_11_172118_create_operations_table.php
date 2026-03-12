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

        Schema::create('operations', function (Blueprint $table) {
            $table->id('id_operation')->primary();
            $table->integer('amount')->nullable();
            $table->string('reason')->nullable();
            $table->string('type_operation')->nullable();
            $table->string('username')->nullable();
            $table->string('designation')->nullable();
            $table->string('description')->nullable();
            $table->string('name_of_second_party')->nullable();
            $table->string('number_of_second_party')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
