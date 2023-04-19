<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadersPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaders_payments', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['in_progress','failed','failed_attempt','paid','to_refund','refund_in_progress','refunded','error_to_refund'])->default('in_progress');
            $table->bigInteger('leader_id');
            $table->bigInteger('leader_payment_method_id');
            $table->string('uuid')->nullable(true);
            $table->enum('currency_type', ['morgi', 'micro_morgi'])->default('morgi');
            $table->double('amount');
            $table->double('dollar_amount');
            $table->text('note')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('ccbill_subscriptionId')->nullable(true);
            $table->string('ccbill_failureReason')->nullable(true);
            $table->string('ccbill_failureCode')->nullable(true);
            $table->string('refund_reason')->nullable(true);
            $table->dateTime('refund_date')->nullable(true);
            $table->string('ip_address')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaders_payments');
    }
}
