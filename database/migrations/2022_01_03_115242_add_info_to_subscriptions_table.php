<?php

use App\Models\Subscription;
use App\Utils\SubscriptionUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('next_donation_at')->after('last_subscription_at');
            $table->timestamp('valid_until_at')->after('last_subscription_at');
        });

        $subscriptions = Subscription::all();
        foreach ($subscriptions as $subscription){
            $next_donation_at = SubscriptionUtils::computeNextDonationAt($subscription->subscription_at, $subscription->last_subscription_at);
            $subscription->update(['next_donation_at' => $next_donation_at, 'valid_until_at' => $next_donation_at]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('next_donation_at');
            $table->dropColumn('valid_until_at');
        });
    }
}
