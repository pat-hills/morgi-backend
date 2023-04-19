<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_rookies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_id');
            $table->bigInteger('rookie_id');
            $table->string('reference', 191);
            $table->enum('status', ['pending', 'successful', 'declined', 'returned'])->default('pending');
            $table->float('amount');
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
        Schema::dropIfExists('payments_rookies');
    }
}
