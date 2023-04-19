<?php

use App\Models\Leader;
use App\Models\Rookie;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeAllFkUnsigned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('chat_attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_id')->change();
            $table->unsignedBigInteger('receiver_id')->change();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->change();
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->unsignedBigInteger('user_reported')->change();
            $table->unsignedBigInteger('reported_by')->change();
            $table->unsignedBigInteger('type_id')->change();
        });

        Schema::table('complaints_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('complaint_id')->change();
        });

        Schema::table('complaints_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('complaint_id')->change();
        });

        Schema::table('content_editors', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('complaints_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('complaint_id')->change();
        });

        /*Schema::table('emails', function (Blueprint $table) {
            $table->unsignedBigInteger('sendgrid_id')->change();
        });*/

        Schema::table('events_actions_history', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('event_id')->change();
        });

        Schema::table('events_photos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('event_id')->change();
        });

        Schema::table('events_photos_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('event_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('events_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('event_status_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('party_type_id')->change();
            $table->unsignedBigInteger('country_id')->change();
        });

        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
        });

        Schema::table('leaders_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('leader_payment_id')->change();
        });

        Schema::table('leaders_packages_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_package_id')->change();
        });

        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('leader_payment_method_id')->change();
            $table->unsignedBigInteger('refund_by')->change();
        });

        Schema::table('merch_actions_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('merch_id')->change();
        });

        Schema::table('merch_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('country_id')->change();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('notification_type_id')->change();
            $table->unsignedBigInteger('ref_user_id')->change();
        });

        Schema::table('password_resets_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('paths', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->change();
            $table->unsignedBigInteger('parent_id')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_period_id')->change();
            $table->unsignedBigInteger('payment_platform_id')->change();
        });

        Schema::table('payments_platforms_rookies', function (Blueprint $table) {
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('payment_platform_id')->change();
        });

        Schema::table('payments_platforms_rookies_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('payments_platforms_rookies_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('payment_platform_id')->change();
        });

        Schema::table('payments_rookies', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('photos_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('profiles_alerts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('code_id')->change();
        });

        Schema::table('profiles_alerts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('code_id')->change();
        });

        Schema::table('pubnub_channels_users', function (Blueprint $table) {
            $table->unsignedBigInteger('channel_id')->change();
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('pubnub_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('pubnub_groups_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->change();
            $table->unsignedBigInteger('channel_id')->change();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->change();
        });

        Schema::table('rookies_blocks', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
        });

        Schema::table('rookies_of_the_days', function (Blueprint $table) {
            $table->unsignedBigInteger('rookie_id')->change();
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('rookie_id')->change();
        });

        Schema::table('rookies_saved', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('photo_id')->change();
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
        });

        Schema::table('rookies_seen_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('photo_id')->change();
        });

        Schema::table('rookies_winners_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('rookie_id')->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('photo_id')->change();
            $table->unsignedBigInteger('leader_payment_method_id')->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('transaction_type_id')->change();
            $table->unsignedBigInteger('subscription_id')->change();
            $table->unsignedBigInteger('payment_rookie_id')->change();
            $table->unsignedBigInteger('leader_payment_id')->change();
        });

        Schema::table('transactions_failed', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_id')->change();
        });

        Schema::table('transactions_handkshake', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('subscription_id')->change();
            $table->unsignedBigInteger('leader_payment_id')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('gender_id')->change();
            $table->unsignedBigInteger('signup_country_id')->change();
            $table->unsignedBigInteger('group_id')->change();
        });

        Schema::table('users_blocked_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('users_descriptions_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('users_identities_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('users_identities_documents_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('users_login_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('users_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('users_paths', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('path_id')->change();
        });

        Schema::table('users_rejected_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('users_rejected_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('videos_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('videos_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->change();
            $table->unsignedBigInteger('region_id')->change();
            $table->unsignedBigInteger('city_id')->change();
            $table->unsignedBigInteger('user_id');
        });

        $rookies = Rookie::all();
        foreach ($rookies as $rookie){
            $rookie->user_id = $rookie->id;
            $rookie->save();
            $rookie->update(['user_id' => $rookie->id]);
        }

        Schema::table('leaders', function (Blueprint $table) {
            $table->unsignedBigInteger('interested_in_gender_id')->change();
            $table->unsignedBigInteger('user_id');
        });

        $leaders = Leader::all();
        foreach ($leaders as $leader){
            $leader->user_id = $leader->id;
            $leader->save();
            $leader->update(['user_id' => $leader->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('chat_attachments', function (Blueprint $table) {
            $table->bigInteger('sender_id')->change();
            $table->bigInteger('receiver_id')->change();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->bigInteger('created_by')->change();
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->bigInteger('user_reported')->change();
            $table->bigInteger('reported_by')->change();
            $table->bigInteger('type_id')->change();
        });

        Schema::table('complaints_histories', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('complaint_id')->change();
        });

        Schema::table('complaints_notes', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('complaint_id')->change();
        });

        Schema::table('content_editors', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('complaints_notes', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('complaint_id')->change();
        });

        /*Schema::table('emails', function (Blueprint $table) {
            $table->bigInteger('sendgrid_id')->change();
        });*/

        Schema::table('events_actions_history', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('event_id')->change();
        });

        Schema::table('events_photos', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('event_id')->change();
        });

        Schema::table('events_photos_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('event_id')->change();
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('events_requests', function (Blueprint $table) {
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('event_status_id')->change();
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('party_type_id')->change();
            $table->bigInteger('country_id')->change();
        });

        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
        });

        Schema::table('leaders_packages', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('leader_payment_id')->change();
        });

        Schema::table('leaders_packages_transactions', function (Blueprint $table) {
            $table->bigInteger('leader_package_id')->change();
        });

        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('leader_payment_method_id')->change();
            $table->bigInteger('refund_by')->change();
        });

        Schema::table('merch_actions_histories', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('merch_id')->change();
        });

        Schema::table('merch_requests', function (Blueprint $table) {
            $table->bigInteger('event_id')->change();
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('country_id')->change();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('notification_type_id')->change();
            $table->bigInteger('ref_user_id')->change();
        });

        Schema::table('password_resets_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });

        Schema::table('paths', function (Blueprint $table) {
            $table->bigInteger('created_by')->change();
            $table->bigInteger('parent_id')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('payment_period_id')->change();
            $table->bigInteger('payment_platform_id')->change();
        });

        Schema::table('payments_platforms_rookies', function (Blueprint $table) {
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('payment_platform_id')->change();
        });

        Schema::table('payments_platforms_rookies_histories', function (Blueprint $table) {
            $table->bigInteger('payments_platforms_rookies_id')->change();
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('payment_platform_id')->change();
        });

        Schema::table('payments_rookies', function (Blueprint $table) {
            $table->bigInteger('payment_id')->change();
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });

        Schema::table('photos_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('profiles_alerts', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('code_id')->change();
        });

        Schema::table('profiles_alerts', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('code_id')->change();
        });

        Schema::table('pubnub_channels_users', function (Blueprint $table) {
            $table->bigInteger('channel_id')->change();
            $table->bigInteger('user_id')->change();
        });

        Schema::table('pubnub_groups', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });

        Schema::table('pubnub_groups_channels', function (Blueprint $table) {
            $table->bigInteger('group_id')->change();
            $table->bigInteger('channel_id')->change();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->bigInteger('country_id')->change();
        });

        Schema::table('rookies_blocks', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('rookie_id')->change();
        });

        Schema::table('rookies_of_the_days', function (Blueprint $table) {
            $table->bigInteger('rookie_id')->change();
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->bigInteger('rookie_id')->change();
        });

        Schema::table('rookies_saved', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('photo_id')->change();
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('rookie_id')->change();
        });

        Schema::table('rookies_seen_histories', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('photo_id')->change();
        });

        Schema::table('rookies_winners_histories', function (Blueprint $table) {
            $table->bigInteger('rookie_id')->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('photo_id')->change();
            $table->bigInteger('leader_payment_method_id')->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('transaction_type_id')->change();
            $table->bigInteger('subscription_id')->change();
            $table->bigInteger('payment_rookie_id')->change();
            $table->bigInteger('leader_payment_id')->change();
        });

        Schema::table('transactions_failed', function (Blueprint $table) {
            $table->bigInteger('subscription_id')->change();
        });

        Schema::table('transactions_handkshake', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('subscription_id')->change();
            $table->bigInteger('leader_payment_id')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('gender_id')->change();
            $table->bigInteger('signup_country_id')->change();
            $table->bigInteger('group_id')->change();
        });

        Schema::table('users_blocked_histories', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('user_id')->change();
        });

        Schema::table('users_descriptions_histories', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('user_id')->change();
        });

        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });

        Schema::table('users_identities_documents', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });

        Schema::table('users_identities_documents_histories', function (Blueprint $table) {
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('user_id')->change();
        });

        Schema::table('users_login_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });

        Schema::table('users_notes', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('users_paths', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('path_id')->change();
        });

        Schema::table('users_rejected_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('users_rejected_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });

        Schema::table('videos_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
            $table->bigInteger('admin_id')->change();
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->bigInteger('country_id')->change();
            $table->bigInteger('region_id')->change();
            $table->bigInteger('city_id')->change();
            $table->dropColumn('user_id');
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->bigInteger('interested_in_gender_id')->change();
            $table->dropColumn('user_id');
        });
    }
}
