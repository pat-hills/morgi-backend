<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('external_id')->unique();
            $table->enum('type', ['chat', 'gift', 'withdrawal', 'refund', 'bonus', 'withdrawal_rejected']);
            $table->bigInteger('referal_external_id')->nullable(true);
            $table->bigInteger('to_user_id');
            $table->bigInteger('from_user_id')->nullable(true);
            $table->bigInteger('transaction_type_id'); //description
            $table->bigInteger('withdrawal_reference')->nullable(true);
            $table->integer('amount_morgi')->nullable(true);
            $table->integer('amount_micromorgi')->nullable(true);
            $table->integer('amount_dollars');
            $table->bigInteger('payment_rookie_id')->nullable(true);
            $table->bigInteger('balance_transaction_id')->nullable(true);
            $table->text('notes')->nullable(true);
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
        Schema::dropIfExists('transactions');
    }
}
