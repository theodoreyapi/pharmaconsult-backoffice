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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_payment')->primary();
            $table->integer('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('description')->nullable();
            $table->string('metadata')->nullable();
            $table->string('operator_id')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('cel_phone_num')->nullable();
            $table->double('cpm_amount')->nullable();
            $table->string('cpm_currency')->nullable();
            $table->string('cpm_custom')->nullable();
            $table->string('cpm_designation')->nullable();
            $table->string('cpm_error_message')->nullable();
            $table->string('cpm_language')->nullable();
            $table->string('cpm_page_action')->nullable();
            $table->string('cpm_payment_config')->nullable();
            $table->string('cpm_phone_prefixe')->nullable();
            $table->string('cpm_site_id')->nullable();
            $table->string('cpm_status')->nullable();
            $table->dateTime('cpm_trans_date')->nullable();
            $table->string('cpm_trans_id')->nullable();
            $table->string('cpm_version')->nullable();
            $table->string('signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
