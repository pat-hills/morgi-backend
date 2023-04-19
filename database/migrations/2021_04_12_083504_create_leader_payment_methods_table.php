<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderPaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leader_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('leader_id');
            $table->boolean('active')->default(false);
            $table->boolean('error')->default(false);
            $table->bigInteger('subscriptionId')->nullable();
            $table->integer('clientAccnum')->nullable();
            $table->integer('clientSubacc')->nullable();
            $table->integer('subscriptionCurrencyCode')->nullable();
            $table->string('cardType')->nullable();
            $table->integer('last4')->nullable();
            $table->string('uuid');
            $table->string('expDate')->nullable();
            $table->string('paymentAccount')->nullable();
            $table->string('ipAddress')->nullable();
            $table->bigInteger('reservationId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leader_payment_methods');
    }
}
