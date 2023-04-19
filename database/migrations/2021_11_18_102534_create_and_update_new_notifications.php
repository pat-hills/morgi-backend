<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\NotificationType;

class CreateAndUpdateNewNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $new_notifications = [
            ['user_type' => 'both', 'type' => 'user_accepted', 'title' => 'Great news!', 'content' => 'Your Morgi account has been approved, welcome to Morgi and good luck!'],
            ['user_type' => 'rookie', 'type' => 'rookie_compliance_refund', 'title' => 'OH NO!', 'content' => 'Some funds have been refunded, please contact customer support for more information'],
            ['user_type' => 'leader', 'type' => 'leader_compliance_refund', 'title' => 'OH NO!', 'content' => 'Your payment was not processed, please contact customer support for more information']
        ];

        foreach ($new_notifications as $notification){

            NotificationType::create($notification);
        }

        NotificationType::where('type', 'rookie_rejected_payment_id_card')->first()->update(['content' => 'Your payment has been carried over to the next payment date as you have not yet uploaded your ID. Click HERE to upload your ID']);
        NotificationType::where('type', 'rookie_rejected_payment_no_method')->first()->update(['content' => 'Your payment has been carried over to the next payment date as you do not have any payment method chosen. Please contact Morgi Customer Service for help with payment methods']);
        NotificationType::where('type', 'rookie_rejected_payment_general')->first()->update(['content' => 'Oh no, your payment was rejected. Please contact Morgi Customer Service for more information']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        NotificationType::where('type', 'user_accepted')->first()->delete();
        NotificationType::where('type', 'rookie_compliance_refund')->first()->delete();
        NotificationType::where('type', 'leader_compliance_refund')->first()->delete();
        NotificationType::where('type', 'rookie_rejected_payment_id_card')->first()->update(['content' => 'Your payment was rejected for ID not being uploaded, the payment will be pushed to the next payment date']);
        NotificationType::where('type', 'rookie_rejected_payment_no_method')->first()->update(['content' => 'Your payment was rejected because you dont have a payment method, the payment will be pushed to the next payment date']);
        NotificationType::where('type', 'rookie_rejected_payment_general')->first()->update(['content' => 'Your payment was rejected. For more info contact Customer Support']);
    }
}
