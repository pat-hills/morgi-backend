<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('emails')->truncate();

        $emails = [
            ['id' => 1, 'type' => 'ACCOUNT_ACTIVATION', 'sendgrid_id' => 'd-794c4d83fdbc4810ae0140814467ccd9', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'type' => 'PASSWORD_RESET', 'sendgrid_id' => 'd-c470a0b38fbf4441bdcf5e16ec23c0c4', 'created_at' => now(), 'updated_at' => now()]
        ];


        \Illuminate\Support\Facades\DB::table('emails')->insert($emails[0]);
        \Illuminate\Support\Facades\DB::table('emails')->insert($emails[1]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::table('emails')->truncate();
    }
}
