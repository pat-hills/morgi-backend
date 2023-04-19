<?php

use App\Models\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanceledAtAttributeToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable(true)->after('last_subscription_at');
        });

        $subscriptions = Subscription::query()->where('status', 'canceled')->get();

        foreach ($subscriptions as $subscription){
            $subscription->update(['canceled_at' => $subscription->updated_at]);
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
            $table->dropColumn('canceled_at');
        });
    }
}
