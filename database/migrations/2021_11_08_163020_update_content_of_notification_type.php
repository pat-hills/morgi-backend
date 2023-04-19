<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContentOfNotificationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $notication = \App\Models\NotificationType::query()
            ->where('type', 'username_changed')
            ->first();
        $notication->update(['content' => 'Your username was updated by customer support']);

        $notication = \App\Models\NotificationType::query()
            ->where('type', 'rookie_birth_date_changed')
            ->first();
        $notication->update(['content' => 'Your birth date was updated by customer support']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $notication = \App\Models\NotificationType::query()
            ->where('type', 'username_changed')
            ->first();
        $notication->update(['content' => 'Your username was updated by customer support in <new_username>']);

        $notication = \App\Models\NotificationType::query()
            ->where('type', 'rookie_birth_date_changed')
            ->first();
        $notication->update(['content' => 'Your birth date was updated by customer support in <new_birth_date>']);
    }
}
