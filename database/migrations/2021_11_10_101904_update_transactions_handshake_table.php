<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsHandshakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('transactions_handkshake', 'transactions_handshake');

        Schema::table('transactions_handshake', function (Blueprint $table) {
            $table->renameColumn('leader_payment_method_id', 'leader_payment_id');
            $table->string('status')->default('default')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('transactions_handshake', 'transactions_handkshake');

        Schema::table('transactions_handkshake', function (Blueprint $table) {
            $table->renameColumn('leader_payment_id', 'leader_payment_method_id');
            $table->string('status')->default('default')->change();
        });
    }
}
