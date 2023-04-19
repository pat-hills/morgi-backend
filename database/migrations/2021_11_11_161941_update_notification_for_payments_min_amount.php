<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationForPaymentsMinAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $notification = \App\Models\NotificationType::query()->where('type', 'rookie_rejected_payment_min_50_usd')->first();
        $notification->update([
            'type' => 'rookie_rejected_payment_min_usd',
            'content' => 'Your payment was postponed to the next payment date because you dont reached the min of <amount> USD'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $notification = \App\Models\NotificationType::query()->where('type', 'rookie_rejected_payment_min_usd')->first();
        $notification->update([
            'type' => 'rookie_rejected_payment_min_50_usd',
            'content' => 'Your payment was postponed to the next payment date because you dont reached the min of 50 USD'
        ]);
    }
}
