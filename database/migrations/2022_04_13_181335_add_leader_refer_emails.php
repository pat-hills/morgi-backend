<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderReferEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Email::query()->create([
            'type' => 'LEADER_REFER_ROOKIE',
            'sendgrid_id' => 'd-11b836d31a274db18d9b11aa7f9c175d'
        ]);

        \App\Models\Email::query()->create([
            'type' => 'ROOKIE_JOINED_FROM_LEADER_REFER',
            'sendgrid_id' => 'd-69f93332afe7489091e2f6dbc43ff3fb'
        ]);
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
