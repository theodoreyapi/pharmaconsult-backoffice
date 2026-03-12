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
        Schema::create('transfert', function (Blueprint $table) {
            $table->id('id_transfert')->primary();
            $table->double('amount')->nullable();
            $table->string('receiver_username')->nullable();
            $table->string('sender_username')->nullable();
            $table->string('execute_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfert');
    }
};
