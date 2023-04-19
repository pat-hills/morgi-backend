<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGiftInactivityReminderNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\NotificationType::query()->where('type', 'gift_inactivity_reminder')->update([
            'content' => "If you won't answer <ref_username> gift within the next day,
                          Morgi will take this gift back and allow <ref_username> to gift another Rookie instead of you."
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
