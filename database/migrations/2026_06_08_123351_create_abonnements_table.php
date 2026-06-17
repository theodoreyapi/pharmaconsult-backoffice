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
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id('id_abonnement')->primary();
            $table->string('plan_name')->comment('Plan Pro, Plan Starter...');
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['MENSUEL', 'ANNUEL'])->default('MENSUEL');
            $table->date('start_date');
            $table->date('renewal_date');
            $table->enum('status', ['ACTIF', 'EXPIRE', 'SUSPENDU', 'ESSAI'])->default('ESSAI');
            $table->integer('max_patients')->default(500);
            $table->integer('max_messages_per_month')->default(3000);
            $table->integer('max_campaigns')->default(20);
            $table->integer('max_team_members')->default(10);
            $table->string('checkout_session_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status_payment')->nullable();
            $table->string('payment_method')->nullable();

            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnements');
        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id']);
            $table->dropColumn('pharmacy_id');
        });
    }
};
