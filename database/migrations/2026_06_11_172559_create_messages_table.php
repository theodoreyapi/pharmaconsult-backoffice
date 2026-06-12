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
        Schema::create('messages', function (Blueprint $table) {
            $table->id('id_message')->primary();

            $table->string('title')->nullable();

            $table->enum('channel', [
                'WHATSAPP',
                'SMS',
                'PUSH'
            ]);

            $table->enum('category', [
                'TRANSACTIONNEL',
                'EDUCATIF',
                'PROMOTIONNEL',
                'CAMPAGNE',
                'PERSONNALISE'
            ])->default('PERSONNALISE');

            $table->longText('content');

            $table->enum('sending_mode', [
                'MANUEL',
                'AUTOMATIQUE'
            ])->default('MANUEL');

            $table->unsignedBigInteger('pharmacien_id');

            $table->foreign('pharmacien_id')
                ->references('id_pharmacien')
                ->on('pharmacien');

            $table->timestamp('scheduled_at')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['pharmacien_id']);
            $table->dropColumn('pharmacien_id');
        });
    }
};
