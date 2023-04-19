<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailedTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_id')->unique();
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failed_transactions');
    }
}
