<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGivebackNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->create(['user_type' => 'leader',
            'type' => 'giveback', 'title' => 'Morgi appreciates you!',
            'content' => 'You have just opened your <amount>rd connection and <amount_micromorgi> Micro Morgis have been added to your wallet.']);

        \App\Models\NotificationType::query()
            ->where('type', 'leader_first_gift_to_rookie')
            ->update([
                'title' => "You're amazing!",
                'content' => "You have just gifted your very first gift in Morgi. Morgi Appreciates You page is now opened for you and 10 Micro Morgis have been added to your wallet."
            ]);
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
