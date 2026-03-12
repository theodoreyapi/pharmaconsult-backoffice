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
        Schema::create('users_pharma', function (Blueprint $table) {
            $table->id('id_user')->primary();
            $table->string('active')->nullable();
            $table->string('email')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('password');
            $table->string('phone_number')->unique();
            $table->string('role')->nullable();
            $table->string('about_me')->nullable();
            $table->string('country')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('username')->unique();
            $table->double('amount')->default(0);
            $table->double('last_amount')->default(0);
            $table->integer('otp_code')->nullable();
            $table->timestamp('otp_expire_at')->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_pharma');
    }
};
