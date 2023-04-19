<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRookieBlockedToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('rookie_blocked_leader')->default(false)->after('last_subscription_at');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('rookie_blocked_leader')->default(false)->after('refunded_by');
        });

        DB::statement("ALTER TABLE transactions_types CHANGE COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi', 'rookie_block_leader') NOT NULL");


        \App\Models\TransactionType::create([
            'type' => 'rookie_block_leader', 'lang' => 'EN', 'description_rookie' => 'Canceled connection with <leader_full_name> refund',
            'description_leader' => 'Canceled connection with <rookie_full_name> refund']);

        \App\Models\NotificationType::create(['user_type' => 'rookie', 'type' => 'blocked_leader',
            'title' => 'You Blocked a Leader!', 'content' => "We would like to inform you that <amount> was deducted from your earnings due to the block you made on <ref_username>.
            Any future monthly gifts have also been cancelled.
            We recommend contacting customer support before blocking to see if we can solve any concerns you may have with the Leader."]);

        \App\Models\NotificationType::create(['user_type' => 'leader', 'type' => 'rookie_blocked_leader',
            'title' => 'Rookie Blocked You!', 'content' => "We are sorry to see that <ref_username> has decided to stop receiving your mentoring.
            We have refunded you for your last transaction to <ref_username>.
            Please allow 7 days for the funds to be returned to you.
            Your monthly recurring gift has also been cancelled.
            We are sure there are many Rookies who would love to receive mentoring from you so don’t give up on passing on your knowledge."]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('rookie_blocked_leader');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('rookie_blocked_leader');
        });

        DB::statement("ALTER TABLE transactions_types CHANGE COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi') NOT NULL");
    }
}
