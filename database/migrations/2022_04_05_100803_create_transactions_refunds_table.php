<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_refunds', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'approved', 'failed'])->default('pending');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('failed_at')->nullable();
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
        Schema::dropIfExists('transactions_refunds');
    }
}
