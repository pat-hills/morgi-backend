<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOldMmTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\LeaderPayment::query()->where('currency_type', 'micro_morgi')->delete();
        \App\Models\Leader::query()->update(['micro_morgi_balance' => 0]);
        \App\Models\Transaction::query()->where('type', 'chat')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
