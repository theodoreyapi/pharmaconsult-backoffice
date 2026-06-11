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
        Schema::create('sessions_notification_settings', function (Blueprint $table) {
            $table->id('id_notification_setting')->primary();

            $table->boolean('new_clinical_alerts')->default(true);
            $table->boolean('late_renewals')->default(true);
            $table->boolean('new_patient_added')->default(false);
            $table->boolean('message_status')->default(true);
            $table->boolean('active_campaigns')->default(false);
            $table->boolean('twofa_enabled')->default(false);

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
        Schema::dropIfExists('sessions_notification_settings');
        Schema::table('sessions_notification_settings', function (Blueprint $table) {
            $table->dropForeign(['pharmacien_id']);
            $table->dropColumn('pharmacien_id');
        });
    }
};
