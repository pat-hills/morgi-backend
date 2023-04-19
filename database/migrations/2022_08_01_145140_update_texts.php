<?php

use App\Models\NotificationType;
use App\Models\Path;
use App\Models\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTexts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TransactionType::query()->where('type', 'gift')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>', 'description_rookie' => 'MOnthly Recurring GIft from {{leader_full_name}}'
        ]);
        TransactionType::query()->where('type', 'chat')->update([
            'description_leader' => 'Gift Micro Morgi on chat to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>', 'description_rookie' => 'On-chat Micro Morgis gift from {{leader_full_name}}'
        ]);
        TransactionType::query()->where('type', 'bought_micromorgi')->update([
            'description_leader' => 'Purchase of {{micromorgi}} micromorgi', 'description_rookie' => ''
        ]);
        TransactionType::query()->where('type', 'refund')->update([
            'description_leader' => 'System refund following an error to <a href="/{{rookie_username}}">{{rookie_full_name}} (#{{referal_internal_id}})</a>', 'description_rookie' => 'System refund to {{leader_full_name}} (#{{referal_internal_id}})'
        ]);
        TransactionType::query()->where('type', 'withdrawal')->update([
            'description_leader' => '', 'description_rookie' => 'Approved via {{payment_method}} {{payment_info}} at {{payment_approved_at}}: {{taxed_dollars}}$ for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}'
        ]);
        TransactionType::query()->where('type', 'withdrawal_rejected')->update([
            'description_leader' => '', 'description_rookie' => 'Rejected via {{payment_method}} {{payment_info}} at {{payment_rejected_at}}: {{taxed_dollars}}$ for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}'
        ]);
        TransactionType::query()->where('type', 'bonus')->update([
            'description_leader' => 'Morgi system bonus', 'description_rookie' => 'Morgi system bonus'
        ]);
        TransactionType::query()->where('type', 'rookie_block_leader')->update([
            'description_leader' => 'Canceled connection with <a href="/{{rookie_username}}">{{rookie_full_name}}</a> refund', 'description_rookie' => 'Refund to {{leader_full_name}} following your block (#{{referal_internal_id}})'
        ]);
        TransactionType::query()->where('type', 'refund_bonus')->update([
            'description_leader' => 'Morgi system decrease', 'description_rookie' => 'Morgi system decrease'
        ]);
        TransactionType::query()->where('type', 'chargeback')->update([
            'description_leader' => 'System refund following an error to <a href="/{{rookie_username}}">{{rookie_full_name}} (#{{referal_internal_id}})</a>', 'description_rookie' => 'Reduction following a chargeback (#{{referal_internal_id}})'
        ]);
        TransactionType::query()->where('type', 'withdrawal_pending')->update([
            'description_leader' => '', 'description_rookie' => 'Pending via {{payment_method}} {{payment_info}}: {{taxed_dollars}}$ for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}'
        ]);
        TransactionType::query()->where('type', 'fine')->update([
            'description_leader' => 'Morgi system decrease', 'description_rookie' => 'Morgi system decrease'
        ]);
        TransactionType::query()->where('type', 'gift_with_coupon')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>, exchanged for coupon #{{coupon_id}}', 'description_rookie' => 'Refund to {{leader_full_name}} since you have not responded for 3 days'
        ]);
        TransactionType::query()->where('type', 'not_refund_gift_with_coupon')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>, exchanged for coupon #{{coupon_id}}', 'description_rookie' => 'MOnthly Recurring GIft from {{leader_full_name}}'
        ]);
        Path::query()->where('key_name', 'activist')->update([
            'name' => 'Activist', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'actor')->update([
            'name' => 'Actor', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'archaeologist')->update([
            'name' => 'Archaeologist', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'architect')->update([
            'name' => 'Architect', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'bartender')->update([
            'name' => 'Bartender', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'beautician')->update([
            'name' => 'Beautician', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'bodybuilder_')->update([
            'name' => 'Bodybuilder ', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'bodyguard_')->update([
            'name' => 'Bodyguard ', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'business_owner')->update([
            'name' => 'Business Owner', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'captain')->update([
            'name' => 'Captain', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'caregiver')->update([
            'name' => 'Caregiver', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'chef')->update([
            'name' => 'Chef', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'classic_musician')->update([
            'name' => 'Classic Musician', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'coacher')->update([
            'name' => 'Coacher', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'companion')->update([
            'name' => 'Companion', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'concierge')->update([
            'name' => 'Concierge', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'dancer')->update([
            'name' => 'Dancer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'driver')->update([
            'name' => 'Driver', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'economist')->update([
            'name' => 'Economist', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'engineer')->update([
            'name' => 'Engineer', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'entrepreneur')->update([
            'name' => 'Entrepreneur', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'farmer')->update([
            'name' => 'Farmer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'fashion_professional')->update([
            'name' => 'Fashion Professional', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'flight_attendant')->update([
            'name' => 'Flight Attendant', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'good_person')->update([
            'name' => 'Good Person', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'graphic_designer')->update([
            'name' => 'Graphic Designer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'hairdresser')->update([
            'name' => 'Hairdresser', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'influencer')->update([
            'name' => 'Influencer', 'prepend' => 'An'
        ]);
        Path::query()->where('key_name', 'lawyer')->update([
            'name' => 'Lawyer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'leader')->update([
            'name' => 'Leader', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'magician')->update([
            'name' => 'Magician', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'manager')->update([
            'name' => 'Manager', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'medical_doctor')->update([
            'name' => 'Medical Doctor', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'model')->update([
            'name' => 'Model', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'musician')->update([
            'name' => 'Musician', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'mystic')->update([
            'name' => 'Mystic', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'nurse')->update([
            'name' => 'Nurse', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'nutritionist')->update([
            'name' => 'Nutritionist', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'painter')->update([
            'name' => 'Painter', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'good_parent')->update([
            'name' => 'Good Parent', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'politician')->update([
            'name' => 'Politician', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'programmer')->update([
            'name' => 'Programmer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'pilot')->update([
            'name' => 'Pilot', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'preacher')->update([
            'name' => 'Preacher', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'rich')->update([
            'name' => 'Rich', 'prepend' => ''
        ]);
        Path::query()->where('key_name', 'scientist')->update([
            'name' => 'Scientist', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'sculpturer')->update([
            'name' => 'Sculpturer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'singer')->update([
            'name' => 'Singer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'something_else')->update([
            'name' => 'Something Else', 'prepend' => ''
        ]);
        Path::query()->where('key_name', 'social_worker')->update([
            'name' => 'Social Worker', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'sportsman')->update([
            'name' => 'Sportsman', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'star')->update([
            'name' => 'Star', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'teacher')->update([
            'name' => 'Teacher', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'tutor')->update([
            'name' => 'Tutor', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'tour_guide')->update([
            'name' => 'Tour Guide', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'trader')->update([
            'name' => 'Trader', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'veterinarian')->update([
            'name' => 'Veterinarian', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'writer')->update([
            'name' => 'Writer', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'yoga_instructor')->update([
            'name' => 'Yoga Instructor', 'prepend' => 'A'
        ]);
        Path::query()->where('key_name', 'zoologist')->update([
            'name' => 'Zoologist', 'prepend' => 'A'
        ]);
        NotificationType::query()->where('type', 'leader_login')->update([
            'title' => "Welcome to Morgi!", 'content' => "Look for Rookies you’d like to support, to help get them started on their path, either by talking, advising, mentoring and simply listening to them. You can also support Rookies with Morgis – MOnthly Recurring GIfts of cash!"]);
        NotificationType::query()->where('type', 'leader_first_gift_to_rookie')->update([
            'title' => "You're amazing!", 'content' => "You've just gifted your very first gift in Morgi. Morgi appreciates you and 10 Micro Morgis have been added to your wallet!"]);
        NotificationType::query()->where('type', 'leader_buy_micromorgi_package')->update([
            'title' => "Congratulations!", 'content' => "You’ve bought a package of <amount_micromorgi> Micro Morgis worth <amount> <currency>!"]);
        NotificationType::query()->where('type', 'leader_change_gift_amount')->update([
            'title' => "Please note!", 'content' => "You’ve changed your monthly gift amount to <ref_username> from <old_amount> Morgis to <amount> Morgis"]);
        NotificationType::query()->where('type', 'leader_renewed_gift')->update([
            'title' => "Renewed gift!", 'content' => "Your monthly gift to <ref_username> for <amount_morgi> Morgis has been renewed for this month."]);
        NotificationType::query()->where('type', 'rookie_login')->update([
            'title' => "Welcome to Morgi!", 'content' => "Build your profile by following your 10 Rookies must-dos, and get the support you need to start focusing on your path!"]);
        NotificationType::query()->where('type', 'rookie_first_gift_from_leader')->update([
            'title' => "Congratulations!", 'content' => "You’ve received your very first gift, how exciting! Be sure to thank <ref_username> for the <amount_morgi> Morgis they gifted you!"]);
        NotificationType::query()->where('type', 'rookie_receive_micromorgi')->update([
            'title' => "Congratulations!", 'content' => "<ref_username> sent you a gift of <amount_micromorgi> Micro Morgis"]);
        NotificationType::query()->where('type', 'verified_id_card')->update([
            'title' => "Identity Documents Verified!", 'content' => "Congratulations, your Identity Documents have now been verified!"]);
        NotificationType::query()->where('type', 'id_card_rejected')->update([
            'title' => "Identity Documents rejected!", 'content' => "Your Identity Documents has been rejected, due to <reason>"]);
        NotificationType::query()->where('type', 'user_got_bonus')->update([
            'title' => "Bonus from Morgi!", 'content' => "Congratulations, you just got a bonus of <amount_micromorgi> Micro Morgis from Morgi!"]);
        NotificationType::query()->where('type', 'rookie_got_bonus')->update([
            'title' => "Bonus from Morgi!", 'content' => "Congratulations, you just got a bonus of <amount_morgi> Morgis from us at Morgi!"]);
        NotificationType::query()->where('type', 'description_declined')->update([
            'title' => "Description rejected", 'content' => "Your description has been rejected, due to <reason>"]);
        NotificationType::query()->where('type', 'description_approved')->update([
            'title' => "Description approved!", 'content' => "Congratulations, your description has been approved!"]);
        NotificationType::query()->where('type', 'photo_declined')->update([
            'title' => "Photo rejected", 'content' => "Your photo was rejected, due to <reason>"]);
        NotificationType::query()->where('type', 'photo_approved')->update([
            'title' => "Photo approved!", 'content' => "Congratulations, your photo has been approved!"]);
        NotificationType::query()->where('type', 'rookie_video_declined')->update([
            'title' => "Video rejected", 'content' => "Your video has been rejected, due to <reason>"]);
        NotificationType::query()->where('type', 'rookie_video_approved')->update([
            'title' => "Video approved!", 'content' => "Congratulations, your video has been approved!"]);
        NotificationType::query()->where('type', 'rookie_merch_in_elaboration')->update([
            'title' => "Merch request received!", 'content' => "Your merch request has been received!"]);
        NotificationType::query()->where('type', 'rookie_merch_sent')->update([
            'title' => "Merch request sent!", 'content' => "Your merch request is coming!"]);
        NotificationType::query()->where('type', 'rookie_merch_canceled')->update([
            'title' => "Merch request canceled", 'content' => "Your merch request has been canceled"]);
        NotificationType::query()->where('type', 'rookie_rejected_payment_id_card')->update([
            'title' => "Payment postponed", 'content' => "Your payment has been carried over to the next payment date as you have not yet uploaded your photo ID yet. Go to 'edit your profile' to upload it! "]);
        NotificationType::query()->where('type', 'rookie_rejected_payment_no_method')->update([
            'title' => "Payment rejected", 'content' => "Your payment has been carried over to the next payment date as you have not chosen a preferred payment method. Go to 'Your way to get paid' to choose one!"]);
        NotificationType::query()->where('type', 'rookie_rejected_payment_min_usd')->update([
            'title' => "Payment postponed", 'content' => "Your payment has been postponed to the next payment date because you have not reached the min of <amount> USD"]);
        NotificationType::query()->where('type', 'rookie_rejected_payment_general')->update([
            'title' => "Payment rejected", 'content' => "Oh no, your payment has been rejected. Please contact Morgi Customer Support for more information"]);
        NotificationType::query()->where('type', 'rookie_change_gift_amount')->update([
            'title' => "Please note!", 'content' => "Your monthly gift amount from <ref_username> has been edited from <old_amount> Morgi to <amount> Morgi"]);
        NotificationType::query()->where('type', 'rookie_new_gift')->update([
            'title' => "Congratulations!", 'content' => "<ref_username> has gifted you with a MOnthly Recurring GIft of <amount_morgi> Morgis. SAY THANKS ON CHAT NOW😊!"]);
        NotificationType::query()->where('type', 'leader_new_gift')->update([
            'title' => "Congratulations!", 'content' => "You successfully gifted <ref_username> with <amount_morgi> Morgis!"]);
        NotificationType::query()->where('type', 'rookie_renewed_gift')->update([
            'title' => "Renewed gift!", 'content' => "<ref_username> is once again gifting you with <amount_morgi> Morgis per month!. SAY THANKS ON CHAT NOW😊!"]);
        NotificationType::query()->where('type', 'leader_deleted_account')->update([
            'title' => "OH NO!", 'content' => "Unknown has deleted their profile. Any future Morgi gifts from this Morgi Friend will be canceled."]);
        NotificationType::query()->where('type', 'rookie_deleted_account')->update([
            'title' => "OH NO!", 'content' => "Unkown has deleted their profile. Any future Morgi gift to this Rookie will be canceled."]);
        NotificationType::query()->where('type', 'blocked_leader')->update([
            'title' => "You Blocked a Morgi Friend!", 'content' => "We would like to inform you that <amount> has been deducted from your Morgi balance due to your decision to block <ref_username>. Any future monthly gifts have also been canceled. We recommend contacting Customer Support before blocking to see if we can solve any concerns you may have with the Friend."]);
        NotificationType::query()->where('type', 'rookie_blocked_leader')->update([
            'title' => "A Rookie Has Blocked You!", 'content' => "We are sorry to see that <ref_username> has decided to stop receiving mentorship from you. We have refunded you for your last gift to <ref_username>. Please allow 7 days for the funds to be refunded to your account. Your monthly recurring gift to that Rookie has also been canceled."]);
        NotificationType::query()->where('type', 'username_changed')->update([
            'title' => "Username changed!", 'content' => "Your username has been updated by customer support"]);
        NotificationType::query()->where('type', 'rookie_birth_date_changed')->update([
            'title' => "Birth date changed!", 'content' => "Your birth date has been updated by customer support"]);
        NotificationType::query()->where('type', 'invalid_card_subscription_canceled')->update([
            'title' => "OH NO!", 'content' => "Your MOnthly Recurring GIft to <ref_username> has not been re-newed due to an invalid credit card."]);
        NotificationType::query()->where('type', 'leader_canceled_subscription')->update([
            'title' => "OH NO!", 'content' => "Your MOnthly Recurring gift from <ref_username> has not been re-newed."]);
        NotificationType::query()->where('type', 'front_id_card_rejected')->update([
            'title' => "Front photo Identity Document rejected!", 'content' => "<reason>"]);
        NotificationType::query()->where('type', 'back_id_card_rejected')->update([
            'title' => "Back photo Identity Document rejected!", 'content' => "<reason>"]);
        NotificationType::query()->where('type', 'selfie_id_card_rejected')->update([
            'title' => "Selfie photo Identity Document rejected!", 'content' => "<reason>"]);
        NotificationType::query()->where('type', 'rookie_changed_first_name')->update([
            'title' => "Name changed!", 'content' => "Your name has been updated by customer support"]);
        NotificationType::query()->where('type', 'rookie_changed_last_name')->update([
            'title' => "Surname changed!", 'content' => "Your surname has beeen updated by customer support"]);
        NotificationType::query()->where('type', 'user_accepted')->update([
            'title' => "Great news!", 'content' => "Your Morgi account has been approved, welcome to Morgi and good luck!"]);
        NotificationType::query()->where('type', 'rookie_compliance_refund')->update([
            'title' => "OH NO!", 'content' => "Some funds have been reduced from your balance, please contact customer support for more information"]);
        NotificationType::query()->where('type', 'leader_compliance_refund')->update([
            'title' => "OH NO!", 'content' => "Your payment has not been processed, please contact customer support for more information"]);
        NotificationType::query()->where('type', 'user_declined')->update([
            'title' => "OH NO!", 'content' => "<reason>"]);
        NotificationType::query()->where('type', 'rookie_winner_lottery_info')->update([
            'title' => "Welcome to Morgi!", 'content' => "In Morgi you can get money from others and the support you need to kick-start your future. Don't forget to log in often. Not only would this make it more likely your profile would be found by others, but each day, we randomly select 3 users to receive the Treasure Chest (a gift from us of 100 Morgis to your account), but you must claim it within 24 hours or it will be gone"]);
        NotificationType::query()->where('type', 'telegram_bot')->update([
            'title' => "Welcome to Morgi!", 'content' => "It is very important to connect to the Telegram bot and receive an instant push notification that’ll help you stay responsive to your Leaders!"]);
        NotificationType::query()->where('type', 'giveback')->update([
            'title' => "Morgi appreciates you!", 'content' => "You have just opened your <reason> connection and <amount_micromorgi> Micro Morgis have been added to your wallet."]);
        NotificationType::query()->where('type', 'got_bonus_coupon')->update([
            'title' => "You have just received a coupon!", 'content' => "worth <amount_morgi> Morgis as a bonus from our admin"]);
        NotificationType::query()->where('type', 'got_refunded_gift_coupon')->update([
            'title' => "You have just received a coupon!", 'content' => "worth <amount_morgi> Morgis in exchange of your gift to <ref_username>"]);
        NotificationType::query()->where('type', 'gift_inactivity_reminder')->update([
            'title' => "You're about to lose gift!", 'content' => "If you won't answer <ref_username> gift within the next day, Morgi will take this gift back and allow <ref_username> to gift another Rookie instead of you."]);
        NotificationType::query()->where('type', 'gift_refunded_inactivity')->update([
            'title' => "Your gift was refunded!", 'content' => "Your gift from <ref_username> was refunded since you did not respond for 3 days."]);
        NotificationType::query()->where('type', 'leader_blocked_rookie')->update([
            'title' => "A Morgi Friend has blocked you!", 'content' => "We are sorry to see that <ref_username> has decided to end their connection with you. Your path to greatness lies with other Friends!"]);
        NotificationType::query()->where('type', 'leader_referred_rookie')->update([
            'title' => "Referred Rookie is now available online!", 'content' => "Start your journey with <ref_username> by talking and elevating your connection with mentorship, advice or even gifts of cash. You decide! Click here to go the chat channel."]);
        NotificationType::query()->where('type', 'leader_referred_rookie_welcome')->update([
            'title' => "Your referrer is notified that you are online!", 'content' => "Click here to go to your chat channel and start talking to <ref_username> and others, thanking them for inviting you, and showing them how serious you are about your chosen life path. They can even choose to gift you with cash!"]);
        NotificationType::query()->where('type', 'leader_free_subscription')->update([
            'title' => "You have a new connection!", 'content' => "We hope you and <ref_username> will enjoy a fruitful connection. Click here and go to your chat channel now to start talking."]);
        NotificationType::query()->where('type', 'rookie_free_subscription')->update([
            'title' => "You have a new connection!", 'content' => "We hope you and <ref_username> will enjoy a fruitful connection. Click here and go to your chat channel now to start talking."]);
        NotificationType::query()->where('type', 'leader_pause_connection')->update([
            'title' => "Rookie paused your chat channel.", 'content' => "This might happen when <ref_username> is expecting a Monthly Recurring Gift of cash to open the chat channel again and resume the connection."]);
        NotificationType::query()->where('type', 'transaction_goal')->update([
            'title' => "Congratulations!", 'content' => "<ref_username> gave you <amount_micromorgi> Micro Morgi to goal <goal_name>. Goal date ends at <goal_end_date>"]);
        NotificationType::query()->where('type', 'converter_first_message')->update([
            'title' => "Your Lucky Match just sent you a message!", 'content' => "A message from <ref_username>, is waiting for your reply. We wish you a fruitful connection!"]);
        NotificationType::query()->where('type', 'photos_approved')->update([
            'title' => "Photos approved!", 'content' => "Congratulations, your photos was approved!"]);
        NotificationType::query()->where('type', 'updates_rejected')->update([
            'title' => "Updates rejected!", 'content' => "Your updates have been rejected, <reason>"]);

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
