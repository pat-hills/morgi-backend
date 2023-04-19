<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeUsernameAndBirthdayFieldsToNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()
            ->create([
                'user_type' => 'both',
                'type' => 'username_changed',
                'title' => 'Username changed!',
                'content' => 'Your username was updated by customer support in <new_username>'
            ]);

        \App\Models\NotificationType::query()
            ->create([
                'user_type' => 'rookie',
                'type' => 'rookie_birth_date_changed',
                'title' => 'Birth date changed!',
                'content' => 'Your birth date was updated by customer support in <new_birth_date>'
            ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        \App\Models\NotificationType::query()
            ->where('type', 'username_changed')->delete();

        \App\Models\NotificationType::query()
            ->where('type', 'rookie_birth_date_changed')->delete();
    }

}
