<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->create(['user_type' => 'leader',
            'type' => 'got_bonus_coupon', 'title' => 'You have just received a coupon',
            'content' => 'worth <amount_morgi> Morgis as a bonus from our admin']);

        \App\Models\NotificationType::query()->create(['user_type' => 'leader',
            'type' => 'got_refunded_gift_coupon', 'title' => 'You have just received a coupon',
            'content' => 'worth <amount_morgi> Morgis in exchange of your gift to <ref_username>']);

        \App\Models\NotificationType::query()->create(['user_type' => 'rookie',
            'type' => 'gift_inactivity_reminder', 'title' => "You're about to lose gift!",
            'content' => "If you won't answer <ref_username> gift within the next day.
                          Morgi will take this gift back and allow <ref_username> to gift another Rookie instead of you."]);

        \App\Models\NotificationType::query()->create(['user_type' => 'rookie',
            'type' => 'gift_refunded_inactivity', 'title' => "Your gift was refunded!",
            'content' => "Your gift from <ref_username> was refunded since you did not respond for 3 days."]);

        \App\Models\Email::create(['type' => 'ROOKIE_INACTIVITY_REMINDER', 'sendgrid_id' => 'd-d533295e931f4c019b8e0778d7877355']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_notifications');
    }
}
