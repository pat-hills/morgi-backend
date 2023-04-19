<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferralEmailType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('emails', function (Blueprint $table) {
            $table->string('sendgrid_id')->change();
        });

        \App\Models\Email::create(['type' => 'REFERRAL', 'sendgrid_id' => 'd-69d605d7d166461289760aff5762fec7']);
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
