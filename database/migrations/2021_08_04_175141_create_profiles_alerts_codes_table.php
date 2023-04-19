<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesAlertsCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $codes = [
            'PA_ROOKIE_001' => 'Your account is pending approval',
            'PA_ROOKIE_002' => 'Your account is now approved',
            'PA_ROOKIE_003' => 'You need to complete ID Verification and enter a payment method',
            'PA_LEADER_001' => 'There were some problem with your credit car',
            'PA_LEADER_002' => 'There were some problem with your credit car (with transactions failed count)'
        ];

        Schema::create('profiles_alerts_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('message');
        });

        foreach ($codes as $key=>$code){
            \Illuminate\Support\Facades\DB::table('profiles_alerts_codes')->insert(['code' => $key, 'message' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles_alerts_codes');
    }
}
