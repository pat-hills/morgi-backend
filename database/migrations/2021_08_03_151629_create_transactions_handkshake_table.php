<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsHandkshakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_handkshake', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('rookie_id')->nullable(true);
            $table->string('type');
            $table->string('status');
            $table->text('jpost_url');
            $table->double('amount');
            $table->double('dollar_amount');
            $table->bigInteger('subscription_id')->nullable(true);
            $table->bigInteger('leader_payment_id')->nullable(true);
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
        Schema::dropIfExists('transactions_handkshake');
    }
}
