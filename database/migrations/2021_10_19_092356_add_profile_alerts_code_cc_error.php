<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileAlertsCodeCcError extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\ProfileAlertCode::query()->create(['code' => 'PA_LEADER_003', 'message' => "We cannot process one or more of your payments with the card connected with this transaction. There is a new credit car saved in your account."]);
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
