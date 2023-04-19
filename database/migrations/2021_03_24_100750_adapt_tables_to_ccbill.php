<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdaptTablesToCcbill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $old_values = [];
        $subs = \App\Models\Subscription::all();
        foreach ($subs as $sub){
            $old_values[$sub->id] = $sub->status;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'canceled', 'unsufficent_funds', 'pending', 'failed'])->default('pending')->after('id');
            $table->string('ccbill_subscriptionId')->nullable();
            $table->string('ccbill_clientAccnum')->nullable();
            $table->string('ccbill_clientSubacc')->nullable();
            $table->string('uuid')->after('amount');
        });

        foreach ($old_values as $key=>$old_value){
            \App\Models\Subscription::find($key)->update(['status' => $old_value]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $old_values = [];
        $subs = \App\Models\Subscription::all();
        foreach ($subs as $sub){
            $old_values[$sub->id] = ($sub->status!=='pending') ? $sub->status : 'active';
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('ccbill_subscriptionId');
            $table->dropColumn('ccbill_clientAccnum');
            $table->dropColumn('ccbill_clientSubacc');
            $table->dropColumn('uuid');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'canceled', 'unsufficent_funds'])->default('active')->after('id');
        });

        foreach ($old_values as $key=>$old_value){
            \App\Models\Subscription::find($key)->update(['status' => $old_value]);
        }
    }
}
