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

            // DEBIT ou CREDIT (du point de vue de l'expéditeur)
            $table->string('type_operation')->comment('DEBIT, CREDIT');

            $table->double('amount')->nullable();
            $table->double('raison')->nullable();

            // Qui envoie
            $table->string('sender_username')->nullable();

            // Qui reçoit
            $table->string('receiver_username')->nullable();

            // Nature du transfert :
            // user_to_user      → user envoie à user
            // user_to_pharmacy  → user envoie à pharmacie
            // pharmacy_to_user  → pharmacie envoie à user
            $table->string('type')->default('user_to_user')
                ->comment('user_to_user, user_to_pharmacy, pharmacy_to_user');

            // Qui a exécuté l'opération
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
