<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImproveTransactionsHandshakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions_handkshake', function (Blueprint $table) {
            $table->text('jpost_url')->nullable(true)->change();
            $table->dropColumn(['subscription_id', 'leader_payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions_handkshake', function (Blueprint $table) {
            $table->text('jpost_url')->nullable(false)->change();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('leader_payment_id');
        });
    }
}
