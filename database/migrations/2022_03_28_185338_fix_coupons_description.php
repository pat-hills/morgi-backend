<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCouponsDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\TransactionType::query()->where('type', 'gift_with_coupon')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/rookie-profile/{{rookie_id}}">{{rookie_full_name}}</a>, exchanged for coupon #{{coupon_id}}',
            'description_rookie' => "Refund to {{leader_full_name}} since you have not responded for 3 days"
        ]);
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
