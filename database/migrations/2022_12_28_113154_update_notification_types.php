<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NotificationType::query()->where('type', "leader_login")->update(['title' => "Welcome to Morgi!", 'content' => "Look for Rookies you’d like to support, to help get them started on their path, either by talking, advising, mentoring, or simply listening to them. You can also support Rookies with Morgis – MOnthly Recurring GIfts of cash!"]);
        NotificationType::query()->where('type', "leader_first_gift_to_rookie")->update(['title' => "You're amazing!", 'content' => "You've just gifted your very first gift in Morgi. Morgi appreciates you and 10 Micro Morgis have been added to your wallet!"]);
        NotificationType::query()->where('type', "leader_buy_micromorgi_package")->update(['title' => "Congratulations!", 'content' => "You’ve bought a package of <amount_micromorgi> Micro Morgis worth <amount> <currency>!"]);
        NotificationType::query()->where('type', "leader_change_gift_amount")->update(['title' => "Please note!", 'content' => "You’ve changed your monthly gift amount from <old_amount> Morgis to <amount> Morgis."]);
        NotificationType::query()->where('type', "leader_renewed_gift")->update(['title' => "Renewed gift!", 'content' => "Your monthly gift of <amount_morgi> Morgis has been renewed for this month!"]);
        NotificationType::query()->where('type', "rookie_login")->update(['title' => "Welcome to Morgi!", 'content' => "Build your profile by following our 10 Rookie Must-Dos, and get the support you need to start focusing on your path!"]);
        NotificationType::query()->where('type', "rookie_first_gift_from_leader")->update(['title' => "Congratulations!", 'content' => "You’ve received your very first gift, how exciting! Be sure to thank <ref_username> for the <amount_morgi> Morgis they gifted you!"]);
        NotificationType::query()->where('type', "rookie_receive_micromorgi")->update(['title' => "Congratulations!", 'content' => "<ref_username> sent you a gift of <amount_micromorgi> Micro Morgis!"]);
        NotificationType::query()->where('type', "verified_id_card")->update(['title' => "Identity Documents Verified!", 'content' => "Congratulations, your identity documents have now been verified!"]);
        NotificationType::query()->where('type', "id_card_rejected")->update(['title' => "Identity Documents rejected!", 'content' => "Your identity documents have been rejected, due to <reason>."]);
        NotificationType::query()->where('type', "user_got_bonus")->update(['title' => "Bonus from Morgi!", 'content' => "Congratulations, you just got a bonus of <amount_micromorgi> Micro Morgis from Morgi!"]);
        NotificationType::query()->where('type', "rookie_got_bonus")->update(['title' => "Bonus from Morgi!", 'content' => "Congratulations, you just got a bonus of <amount_morgi> Morgis from us at Morgi!"]);
        NotificationType::query()->where('type', "description_declined")->update(['title' => "Description rejected.", 'content' => "Your description has been rejected, due to <reason>."]);
        NotificationType::query()->where('type', "description_approved")->update(['title' => "Description approved!", 'content' => "Congratulations, your description has been approved!"]);
        NotificationType::query()->where('type', "photo_declined")->update(['title' => "Photo rejected.", 'content' => "Your photo has been rejected, due to <reason>."]);
        NotificationType::query()->where('type', "photo_approved")->update(['title' => "Photo approved!", 'content' => "Congratulations, your photo has been approved!"]);
        NotificationType::query()->where('type', "rookie_video_declined")->update(['title' => "Video rejected.", 'content' => "Your video has been rejected, due to <reason>."]);
        NotificationType::query()->where('type', "rookie_video_approved")->update(['title' => "Video approved!", 'content' => "Congratulations, your video has been approved!"]);
        NotificationType::query()->where('type', "rookie_merch_in_elaboration")->update(['title' => "Merch request received!", 'content' => "Your merch request has been received!"]);
        NotificationType::query()->where('type', "rookie_merch_sent")->update(['title' => "Merch request sent!", 'content' => "Your merch request is coming!"]);
        NotificationType::query()->where('type', "rookie_merch_canceled")->update(['title' => "Merch request canceled", 'content' => "Your merch request has been cancelled"]);
        NotificationType::query()->where('type', "rookie_rejected_payment_id_card")->update(['title' => "Payment postponed.", 'content' => "Your payment has been carried over to the next payment date as you have not yet uploaded your photo ID yet. Go to 'edit your profile' to upload it!"]);
        NotificationType::query()->where('type', "rookie_rejected_payment_no_method")->update(['title' => "Payment rejected.", 'content' => "Your payment has been carried over to the next payment date as you have not chosen a preferred payment method. Go to 'Your way to get paid' to choose one!"]);
        NotificationType::query()->where('type', "rookie_rejected_payment_min_usd")->update(['title' => "Payment postponed.", 'content' => "Your payment has been postponed to the next payment date because you have not reached the minimum amount of <amount> USD."]);
        NotificationType::query()->where('type', "rookie_rejected_payment_general")->update(['title' => "Payment rejected.", 'content' => "Oh no, your payment has been rejected. Please contact Morgi customer support for more information."]);
        NotificationType::query()->where('type', "rookie_change_gift_amount")->update(['title' => "Please note!", 'content' => "Your monthly gift amount from <ref_username> has been edited from <old_amount> Morgis to <amount> Morgis."]);
        NotificationType::query()->where('type', "rookie_new_gift")->update(['title' => "Congratulations!", 'content' => "<ref_username> has gifted you with a MOnthly Recurring GIft of <amount_morgi> Morgis. SAY THANKS ON CHAT NOW😊!"]);
        NotificationType::query()->where('type', "leader_new_gift")->update(['title' => "Congratulations!", 'content' => "Your gift of <amount_morgi> Morgis was successfully sent!"]);
        NotificationType::query()->where('type', "rookie_renewed_gift")->update(['title' => "Renewed gift!", 'content' => "<ref_username> is once again gifting you with <amount_morgi> Morgis per month!. SAY THANKS ON CHAT NOW😊!"]);
        NotificationType::query()->where('type', "leader_deleted_account")->update(['title' => "OH NO!", 'content' => "<ref_username> has deleted their profile. Any future Morgi gifts from this Morgi Friend are canceled."]);
        NotificationType::query()->where('type', "rookie_deleted_account")->update(['title' => "OH NO!", 'content' => "<ref_username> has deleted their profile. Any future Morgi gifts to this Rookie are canceled."]);
        NotificationType::query()->where('type', "blocked_leader")->update(['title' => "You Blocked a Morgi Friend!", 'content' => "We would like to inform you that <amount> has been deducted from your Morgi balance due to your decision to block <ref_username>. Any future monthly gifts have also been cancelled. We recommend contacting customer support before blocking to see if we can solve any concerns you may have with this Friend."]);
        NotificationType::query()->where('type', "rookie_blocked_leader")->update(['title' => "A Rookie has blocked you!", 'content' => "We are sorry to see that <ref_username> has decided to stop receiving mentorship from you. We have refunded you for your last gift to <ref_username>. Please allow 7 days for the funds to be refunded to your account. Any future Morgi gifts to this Rookie are cancelled, too."]);
        NotificationType::query()->where('type', "username_changed")->update(['title' => "Username changed!", 'content' => "Your username has been updated by customer support."]);
        NotificationType::query()->where('type', "rookie_birth_date_changed")->update(['title' => "Birth date changed!", 'content' => "Your birth date has been updated by customer support."]);
        NotificationType::query()->where('type', "invalid_card_subscription_canceled")->update(['title' => "OH NO!", 'content' => "Your MOnthly Recurring GIft has not been renewed due to an invalid credit card."]);
        NotificationType::query()->where('type', "leader_canceled_subscription")->update(['title' => "OH NO!", 'content' => "Your MOnthly Recurring GIft from <ref_username> has not been renewed."]);
        NotificationType::query()->where('type', "front_id_card_rejected")->update(['title' => "Front photo identity document rejected!", 'content' => "Due to <reason>."]);
        NotificationType::query()->where('type', "back_id_card_rejected")->update(['title' => "Back photo identity document rejected!", 'content' => "Due to <reason>."]);
        NotificationType::query()->where('type', "selfie_id_card_rejected")->update(['title' => "Selfie photo identity document rejected!", 'content' => "Due to <reason>."]);
        NotificationType::query()->where('type', "rookie_changed_first_name")->update(['title' => "First name changed!", 'content' => "Your name has been updated by customer support."]);
        NotificationType::query()->where('type', "rookie_changed_last_name")->update(['title' => "Surname changed!", 'content' => "Your surname has been updated by customer support."]);
        NotificationType::query()->where('type', "user_accepted")->update(['title' => "Great news!", 'content' => "Your Morgi account has been approved, welcome to Morgi and good luck!"]);
        NotificationType::query()->where('type', "rookie_compliance_refund")->update(['title' => "OH NO!", 'content' => "Some funds have been reduced from your balance, please contact customer support for more information."]);
        NotificationType::query()->where('type', "leader_compliance_refund")->update(['title' => "OH NO!", 'content' => "Your payment has not been processed, please contact customer support for more information."]);
        NotificationType::query()->where('type', "rookie_winner_lottery_info")->update(['title' => "Welcome to Morgi!", 'content' => "In Morgi you can get money from others and the support you need to kick-start your future. Don't forget to log in often. Not only would this make it more likely your profile would be found by others, but each day, we randomly select 3 users to receive the Treasure Chest (a gift from us of 100 Morgis to your account), but you must claim it within 24 hours or it will be gone"]);
        NotificationType::query()->where('type', "user_declined")->update(['title' => "OH NO!", 'content' => "Due to <reason>."]);
        NotificationType::query()->where('type', "telegram_bot")->update(['title' => "Welcome to Morgi!", 'content' => "It is crucial to connect to the Telegram Notifications Bot to receive instant push notifications that’ll help you stay responsive to your Morgi Friends!"]);
        NotificationType::query()->where('type', "giveback")->update(['title' => "Morgi appreciates you!", 'content' => "You have just gifted your <reason> Rookie and <amount_micromorgi> Micro Morgis have been added to your wallet!"]);
        NotificationType::query()->where('type', "got_bonus_coupon")->update(['title' => "You have just received a coupon!", 'content' => "Worth <amount_morgi> Morgis as a bonus from the admin!"]);
        NotificationType::query()->where('type', "got_refunded_gift_coupon")->update(['title' => "You have just received a coupon!", 'content' => "Worth <amount_morgi> Morgis in exchange of your gift."]);
        NotificationType::query()->where('type', "gift_inactivity_reminder")->update(['title' => "You're about to lose a gift!", 'content' => "If you won't respond to <ref_username>’s gift within the next day, Morgi will take this gift back and allow <ref_username> to gift another Rookie instead of you."]);
        NotificationType::query()->where('type', "gift_refunded_inactivity")->update(['title' => "Your gift has been refunded!", 'content' => "Your gift from <ref_username> has been refunded since you did not respond for 3 days."]);
        NotificationType::query()->where('type', "leader_blocked_rookie")->update(['title' => "A Morgi Friend has blocked you!", 'content' => "We are sorry to see that Username has decided to end their connection with you. Your path to greatness lies with other Friends!"]);
        NotificationType::query()->where('type', "leader_referred_rookie")->update(['title' => "Referred Rookie is now available online!", 'content' => "Start your journey with your referred Rookie by talking and elevating your connection with mentorship, advice or even gifts of cash. You decide! Click here to go the chat channel."]);
        NotificationType::query()->where('type', "leader_referred_rookie_welcome")->update(['title' => "Your referrer has been notified that you are online!", 'content' => "Click here to go to your chat channel and start talking to <ref_username> and others, thanking them for inviting you, and showing them how serious you are about your chosen life path. They can even choose to gift you with cash!"]);
        NotificationType::query()->where('type', "leader_free_subscription")->update(['title' => "You have a new connection!", 'content' => "We hope you will enjoy a fruitful connection. Click here and go to your chat channel now and start talking!"]);
        NotificationType::query()->where('type', "rookie_free_subscription")->update(['title' => "You have a new connection!", 'content' => "We hope you and <ref_username> will enjoy a fruitful connection. Click here and go to your chat channel now and start talking!"]);
        NotificationType::query()->where('type', "leader_pause_connection")->update(['title' => "Rookie paused your chat channel.", 'content' => "This might happen when a Rookie is expecting a MOnthly Recurring GIft of cash. A gift will open the chat channel again and resume the connection!"]);
        NotificationType::query()->where('type', "converter_first_message")->update(['title' => "Your Lucky Match just sent you a message!", 'content' => "A message is waiting for your reply. We wish you a fruitful connection!"]);
        NotificationType::query()->where('type', "photos_approved")->update(['title' => "Photos approved!", 'content' => "Congratulations, your photos have been approved!"]);
        NotificationType::query()->where('type', "updates_rejected")->update(['title' => "Updates rejected!", 'content' => "Your updates have been rejected, due to <reason>."]);
        NotificationType::query()->where('type', "transaction_goal")->update(['title' => "Congratulations!", 'content' => "<ref_username> gave you <amount_micromorgi> Micro Morgi to support your Goal <goal_name>. Goal date ends at <goal_end_date>"]);
        NotificationType::query()->where('type', "rookie_goal_approved")->update(['title' => "Congratulations!", 'content' => "Your Goal has been approved!"]);
        NotificationType::query()->where('type', "rookie_goal_cancelled")->update(['title' => "OH NO!", 'content' => "Unfortunately, your Goal has been declined due to violations of our terms and conditions. Reason: <reason>. Please create a new Goal. "]);
        NotificationType::query()->where('type', "rookie_goal_suspended")->update(['title' => "OH NO!", 'content' => "Your Goal has been suspended due to violations of our terms and conditions. Please edit the Goal parameters to reactivate it again. Reason: <reason>"]);
        NotificationType::query()->where('type', "rookie_goal_proof_approved")->update(['title' => "Congratulations!", 'content' => "Your Goal proof has been approved!"]);
        NotificationType::query()->where('type', "rookie_goal_proof_declined")->update(['title' => "OH NO!", 'content' => "Unfortunately, your Goal proof has been declined. Please try again with a new proof. Reason: <reason>"]);
        NotificationType::query()->where('type', "rookie_goal_amount_reached")->update(['title' => "Congratulations!", 'content' => "Your Goal has reached at least 75% funding!"]);
        NotificationType::query()->where('type', "leader_goal_completed")->update(['title' => "Great News!", 'content' => "The proofs of your supported Goal are now online!"]);
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
