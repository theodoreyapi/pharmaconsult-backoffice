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
        Schema::create('profile_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id_subscription')->primary();

            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('user_id');

            $table->decimal('amount', 10, 2)->default(1000.00)
                ->comment('1000 FCFA/an par profil');
            $table->string('currency', 10)->default('FCFA');

            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled'])
                ->default('pending');

            $table->date('start_date');
            $table->date('end_date')->comment('start_date + 1 an');

            $table->string('payment_reference', 100)->nullable()
                ->comment('Référence paiement mobile money');

            $table->string('checkout_session_id')->nullable();
            $table->string('payment_method')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->foreign('profile_id')
                ->references('id_profile')->on('health_profiles')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id_user')->on('users_pharma')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_subscriptions');
        Schema::table('profile_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropColumn('profile_id');

            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
