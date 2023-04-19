<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRookieNewGiftNotificationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $notification = \App\Models\NotificationType::query()->where('type','rookie_new_gift')->first();
        $notification->update([
            'content' => '<ref_username> has gifted you with a monthly allowance of <amount_morgi> Morgi. SAY THANKS😊!'
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
        $notification = \App\Models\NotificationType::query()->where('type','rookie_new_gift')->first();
        $notification->update([
            'content' => '<ref_username> has gifted you with a monthly allowance of <amount_morgi>. SAY THANKS😊!'
        ]);
    }
}
