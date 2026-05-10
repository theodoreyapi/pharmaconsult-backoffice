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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('id_appointment')->primary();
            $table->string('reference', 20)->unique()->comment('Ex: RDV-20240504-0001');

            $table->unsignedBigInteger('vaccine_id')->nullable();
            $table->foreign('vaccine_id')->references('id_vaccine')->on('vaccines')->onDelete('cascade');
            $table->unsignedBigInteger('pharmacy_id')->nullable();
            $table->foreign('pharmacy_id')->references('id_pharmacy')->on('pharmacy')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id_user')->on('users_pharma')->onDelete('cascade');

            $table->string('patient_name', 150)->nullable();
            $table->string('patient_phone', 20);
            $table->string('patient_email', 150)->nullable();
            $table->date('appointment_date');

            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                ->default('pending');

            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['vaccine_id', 'pharmacy_id', 'user_id']);
            $table->dropColumn('vaccine_id');
            $table->dropColumn('pharmacy_id');
            $table->dropColumn('user_id');
        });
    }
};
