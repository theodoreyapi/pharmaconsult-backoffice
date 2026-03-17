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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id('id_subscription')->primary();
            $table->string('description')->nullable();
            $table->string('type_service')->nullable();
            $table->integer('duree')->nullable();
            $table->string('status')->nullable();
            $table->string('username')->nullable();
            $table->dateTime('valid_until')->nullable();

            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id_module')->on('modules')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });
    }
};
