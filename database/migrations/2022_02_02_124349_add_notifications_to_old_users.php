<?php

use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationsToOldUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::query()->select('users.*')
            ->join('notifications', 'notifications.user_id', '=', 'users.id')
            ->havingRaw("COUNT(notifications.id)=0")
            ->groupBy('users.id')
            ->get();

        $welcome_id = NotificationType::where('type', 'rookie_login')->first()->id;
        $bot_id = NotificationType::where('type', 'telegram_bot')->first()->id;

        foreach ($users as $user){
            Notification::query()->create([
                'user_id' => $user->id,
                'notification_type_id' => $welcome_id
            ]);

            Notification::query()->create([
                'user_id' => $user->id,
                'notification_type_id' => $bot_id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
