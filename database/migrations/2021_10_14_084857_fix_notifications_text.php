<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixNotificationsText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::where('type', 'rookie_new_gift')->update(['content' => '<ref_username> has gifted you with a monthly allowance of <amount_morgi>. SAY THANKS😊!']);
        \App\Models\NotificationType::where('type', 'rookie_renewed_gift')->update(['content' => '<ref_username> is once again gifting you with <amount_morgi> Morgi per month!. SAY HI😊!']);
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
