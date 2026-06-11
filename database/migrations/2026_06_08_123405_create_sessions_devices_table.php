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
        Schema::create('sessions_devices', function (Blueprint $table) {
            $table->id('id_session_device')->primary();
            $table->string('device_name')->comment('Tablet Samsung (Chrome)');
            $table->string('browser')->nullable();
            $table->string('location')->nullable()->comment('Abidjan, CI');
            $table->string('ip_address')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamp('last_active_at')->nullable();

            $table->unsignedBigInteger('pharmacien_id');
            $table->foreign('pharmacien_id')->references('id_pharmacien')->on('pharmacien')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions_devices');
        Schema::table('sessions_devices', function (Blueprint $table) {
            $table->dropForeign(['pharmacien_id']);
            $table->dropColumn('pharmacien_id');
        });
    }
};
