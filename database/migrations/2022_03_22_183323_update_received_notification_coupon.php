<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReceivedNotificationCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->where('type', 'got_refunded_gift_coupon')->update([
            'title' => 'You have just received a coupon!',
            'content' => 'worth <amount_morgi> Morgis in exchange of your gift to <ref_username>'
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
