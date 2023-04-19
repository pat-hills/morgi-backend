<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateLeaderRenewedNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::where('type', 'leader_renewed_gift')
            ->update(['content' => 'Your monthly gift to <ref_username> for <amount_morgi> Morgi has been renewed for this month.']);

        DB::statement("ALTER TABLE notifications_types CHANGE COLUMN user_type user_type ENUM('rookie', 'leader', 'both') NOT NULL DEFAULT 'both'");

        Schema::table('notifications', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('ref_user_id');
        });

        $notifications = [
            ['user_type' => 'both', 'type' => 'verified_id_card',
                'title' => 'Identity Documents Verified!', 'content' => 'Congratulations, your Identity Documents now are verified!'],

            ['user_type' => 'both', 'type' => 'id_card_rejected',
                'title' => 'Identity Documents rejected!', 'content' => 'Your Identity Documents was rejected, reason <reason>'],

            ['user_type' => 'leader', 'type' => 'leader_got_bonus',
                'title' => 'Bonus from Morgi!', 'content' => 'Congratulations, you got a bonus of <amount_micromorgi> Micromorgi from Morgi!'],

            ['user_type' => 'rookie', 'type' => 'rookie_got_bonus',
                'title' => 'Bonus from Morgi!', 'content' => 'Congratulations, you got a bonus of <amount_morgi> Morgi from Morgi!'],

            ['user_type' => 'both', 'type' => 'description_declined',
                'title' => 'Description rejected', 'content' => 'Your description was rejected, reason <reason>'],

            ['user_type' => 'both', 'type' => 'description_approved',
                'title' => 'Description approved!', 'content' => 'Congratulations, your description was approved!'],

            ['user_type' => 'both', 'type' => 'photo_declined',
                'title' => 'Photo rejected', 'content' => 'Your photo was rejected, reason <reason>'],

            ['user_type' => 'both', 'type' => 'photo_approved',
                'title' => 'Photo approved!', 'content' => 'Congratulations, your photo was approved!'],

            ['user_type' => 'rookie', 'type' => 'rookie_video_declined',
                'title' => 'Video rejected', 'content' => 'Your video was rejected, reason <reason>'],

            ['user_type' => 'rookie', 'type' => 'rookie_video_approved',
                'title' => 'Video approved!', 'content' => 'Congratulations, your video was approved!'],

            ['user_type' => 'rookie', 'type' => 'rookie_merch_in_elaboration',
                'title' => 'Merch request in elaboration!', 'content' => 'Your merch request is in elaboration!'],

            ['user_type' => 'rookie', 'type' => 'rookie_merch_sent',
                'title' => 'Merch request sent!', 'content' => 'Your merch request is coming!'],

            ['user_type' => 'rookie', 'type' => 'rookie_merch_canceled',
                'title' => 'Merch request canceled', 'content' => 'Your merch request was canceled'],

            ['user_type' => 'rookie', 'type' => 'rookie_rejected_payment_id_card',
                'title' => 'Payment rejected', 'content' => 'Your payment was rejected for ID not being uploaded, the payment will be pushed to the next payment date'],

            ['user_type' => 'rookie', 'type' => 'rookie_rejected_payment_no_method',
                'title' => 'Payment rejected', 'content' => 'Your payment was rejected because you dont have a payment method, the payment will be pushed to the next payment date'],

            ['user_type' => 'rookie', 'type' => 'rookie_rejected_payment_min_50_usd',
                'title' => 'Payment postponed', 'content' => 'Your payment was postponed to the next payment date because you dont reached the min of 50 USD'],

            ['user_type' => 'rookie', 'type' => 'rookie_rejected_payment_general',
                'title' => 'Payment rejected', 'content' => 'Your payment was rejected. For more info contact Customer Support'],
        ];

        \App\Models\NotificationType::insert($notifications);
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
