<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAllNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            NotificationType::where("type", "leader_login")->update(["title" => "Welcome to Morgi!", "content" => "Look for people you’d like to help and provide a monthly gift for, to help get them started on their path! At this moment, you can only see male/female Rookies in the feed according to your choice, and you can change it at any time under 'Edit your profile'. In the next few days you will be able to select to see all Rookies' gender!"]);
            NotificationType::where("type", "leader_first_gift_to_rookie")->update(["title" => "You're amazing!", "content" => "You've just gifted your very first gift in Morgi. Morgi appreciates you and 10 Micro Morgis have been added to your wallet!"]);
            NotificationType::where("type", "leader_buy_micromorgi_package")->update(["title" => "Congratulations!", "content" => "You’ve bought a package of <amount_micromorgis> Micro Morgis worth <amount> <currency>!"]);
            NotificationType::where("type", "leader_change_gift_amount")->update(["title" => "Please note!", "content" => "You’ve changed your monthly gift amount to <ref_username> from <old_amount> Morgis to <amount> Morgis"]);
            NotificationType::where("type", "leader_renewed_gift")->update(["title" => "Renewed gift!", "content" => "Your monthly gift to <ref_username> for <amount_morgis> Morgis has been renewed for this month."]);
            NotificationType::where("type", "rookie_login")->update(["title" => "Welcome to Morgi!", "content" => "Build your profile, and get the help you need to start focusing on your path!"]);
            NotificationType::where("type", "rookie_first_gift_from_leader")->update(["title" => "Congratulations!", "content" => "You’ve received your very first gift, how exciting! Be sure to thank <ref_username> for the <amount_morgi> Morgis they gifted you!"]);
            NotificationType::where("type", "rookie_receive_micromorgi")->update(["title" => "Congratulations!", "content" => "<ref_username> sent you a gift of <amount_micromorgis> Micro Morgis"]);
            NotificationType::where("type", "verified_id_card")->update(["title" => "Identity Documents Verified!", "content" => "Congratulations, your Identity Documents have now been verified!"]);
            NotificationType::where("type", "id_card_rejected")->update(["title" => "Identity Documents rejected!", "content" => "Your Identity Documents has been rejected, due to <reason>"]);
            NotificationType::where("type", "user_got_bonus")->update(["title" => "Bonus from Morgi!", "content" => "Congratulations, you just got a bonus of <amount_micromorgis> Micro Morgis from Morgi!"]);
            NotificationType::where("type", "rookie_got_bonus")->update(["title" => "Bonus from Morgi!", "content" => "Congratulations, you just got a bonus of <amount_morgis> Morgis from us at Morgi!"]);
            NotificationType::where("type", "description_declined")->update(["title" => "Description rejected", "content" => "Your description has been rejected, due to <reason>"]);
            NotificationType::where("type", "description_approved")->update(["title" => "Description approved!", "content" => "Congratulations, your description has been approved!"]);
            NotificationType::where("type", "photo_declined")->update(["title" => "Photo rejected", "content" => "Your photo was rejected, due to <reason>"]);
            NotificationType::where("type", "photo_approved")->update(["title" => "Photo approved!", "content" => "Congratulations, your photo has been approved!"]);
            NotificationType::where("type", "rookie_video_declined")->update(["title" => "Video rejected", "content" => "Your video has been rejected, due to <reason>"]);
            NotificationType::where("type", "rookie_video_approved")->update(["title" => "Video approved!", "content" => "Congratulations, your video has been approved!"]);
            NotificationType::where("type", "rookie_merch_in_elaboration")->update(["title" => "Merch request received!", "content" => "Your merch request has been received!"]);
            NotificationType::where("type", "rookie_merch_sent")->update(["title" => "Merch request sent!", "content" => "Your merch request is coming!"]);
            NotificationType::where("type", "rookie_merch_canceled")->update(["title" => "Merch request canceled", "content" => "Your merch request has been canceled"]);
            NotificationType::where("type", "rookie_rejected_payment_id_card")->update(["title" => "Payment postponed", "content" => "Your payment has been carried over to the next payment date as you have not yet uploaded your photo ID yet. Go to 'edit your profile' to upload it! "]);
            NotificationType::where("type", "rookie_rejected_payment_no_method")->update(["title" => "Payment rejected", "content" => "Your payment has been carried over to the next payment date as you have not chosen a preferred payment method. Go to 'your way to get paid' to choose one!"]);
            NotificationType::where("type", "rookie_rejected_payment_min_usd")->update(["title" => "Payment postponed", "content" => "Your payment has been postponed to the next payment date because you have not reached the min of <amount> USD"]);
            NotificationType::where("type", "rookie_rejected_payment_general")->update(["title" => "Payment rejected", "content" => "Oh no, your payment has been rejected. Please contact Morgi Customer Support for more information"]);
            NotificationType::where("type", "rookie_change_gift_amount")->update(["title" => "Please note!", "content" => "Your monthly gift amount from <ref_username> has been edited from <old_amount> Morgi to <amount> Morgi"]);
            NotificationType::where("type", "rookie_new_gift")->update(["title" => "Congratulations!", "content" => "<ref_username> has gifted you with a MOnthly Recurring GIft of <amount_morgis> Morgis. SAY THANKS😊!"]);
            NotificationType::where("type", "leader_new_gift")->update(["title" => "Congratulations!", "content" => "You successfully gifted <ref_username> with <amount_morgis> Morgis!"]);
            NotificationType::where("type", "rookie_renewed_gift")->update(["title" => "Renewed gift!", "content" => "<ref_username> is once again gifting you with <amount_morgis> Morgis per month!. SAY THANKS 😊!"]);
            NotificationType::where("type", "leader_deleted_account")->update(["title" => "OH NO!", "content" => "Unknown has deleted their profile. Any future Morgi gifts from this Leader will be canceled."]);
            NotificationType::where("type", "rookie_deleted_account")->update(["title" => "OH NO!", "content" => "Unkown has deleted their profile. Any future Morgi gift to this Rookie will be canceled."]);
            NotificationType::where("type", "blocked_leader")->update(["title" => "You Blocked a Leader!", "content" => "We would like to inform you that <amount> has been deducted from your Morgi balance due to your decision to block <ref_username>. Any future monthly gifts have also been canceled. We recommend contacting Customer Support before blocking to see if we can solve any concerns you may have with the Leader."]);
            NotificationType::where("type", "rookie_blocked_leader")->update(["title" => "A Rookie Has Blocked You!", "content" => "We are sorry to see that <ref_username> has decided to stop receiving mentorship from you. We have refunded you for your last gift to <ref_username>. Please allow 7 days for the funds to be refunded to your account. Your monthly recurring gift to that Rookie has also been canceled."]);
            NotificationType::where("type", "username_changed")->update(["title" => "Username changed!", "content" => "Your username has been updated by customer support"]);
            NotificationType::where("type", "rookie_birth_date_changed")->update(["title" => "Birth date changed!", "content" => "Your birth date has been updated by customer support"]);
            NotificationType::where("type", "invalid_card_subscription_canceled")->update(["title" => "OH NO!", "content" => "Your MOnthly Recurring GIft to <ref_username> has not been re-newed due to an invalid credit card."]);
            NotificationType::where("type", "leader_canceled_subscription")->update(["title" => "OH NO!", "content" => "Your MOnthly Recurring gift from <ref_username> has not been re-newed."]);
            NotificationType::where("type", "front_id_card_rejected")->update(["title" => "Front photo Identity Document rejected!", "content" => "<reason>"]);
            NotificationType::where("type", "back_id_card_rejected")->update(["title" => "Back photo Identity Document rejected!", "content" => "<reason>"]);
            NotificationType::where("type", "selfie_id_card_rejected")->update(["title" => "Selfie photo Identity Document rejected!", "content" => "<reason>"]);
            NotificationType::where("type", "rookie_changed_first_name")->update(["title" => "Name changed!", "content" => "Your name has been updated by customer support"]);
            NotificationType::where("type", "rookie_changed_last_name")->update(["title" => "Surname changed!", "content" => "Your surname has beeen updated by customer support"]);
            NotificationType::where("type", "user_accepted")->update(["title" => "Great news!", "content" => "Your Morgi account has been approved, welcome to Morgi and good luck!"]);
            NotificationType::where("type", "rookie_compliance_refund")->update(["title" => "OH NO!", "content" => "Some funds have been reduced from your balance, please contact customer support for more information"]);
            NotificationType::where("type", "leader_compliance_refund")->update(["title" => "OH NO!", "content" => "Your payment has not been processed, please contact customer support for more information"]);
            NotificationType::where("type", "user_declined")->update(["title" => "OH NO!", "content" => "<reason>"]);
            NotificationType::where("type", "rookie_winner_lottery_info")->update(["title" => "Welcome to Morgi!", "content" => "In Morgi you can get money from others and the support you need to kick-start your future. Don't forget to log in often. Not only would this make it more likely your profile would be found by others, but each day, we randomly select 3 users to receive the Treasure Chest (a gift from us of 100 Morgis to your account), but you must claim it within 24 hours or it will be gone"]);
            NotificationType::where("type", "telegram_bot")->update(["title" => "Welcome to Morgi!", "content" => "It is very important to connect to the Telegram bot and receive an instant push notification that’ll help you stay responsive to your Leaders!"]);
            \Illuminate\Support\Facades\DB::commit();
        }catch (Exception $exception){
            \Illuminate\Support\Facades\DB::rollBack();
            throw new Exception($exception->getMessage());
        }
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
