<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leader_id');
            $table->double('currency_value');
            $table->unsignedBigInteger('transaction_id');
            $table->boolean('is_spent')->default(false);
            $table->timestamps();
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->integer('total_coupons_got')->after('internal_status')->default(0);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->after('subscription_id')->nullable(true);
        });

        DB::statement("ALTER TABLE transactions_types CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected',
    'bought_micromorgi','rookie_block_leader','refund_bonus','chargeback','withdrawal_pending','fine','gift_with_coupon')
            NOT NULL");

        \App\Models\TransactionType::query()->create([
            'type' => 'gift_with_coupon',
            'description_leader' => 'MOnthly Recurring GIft to <a href="/rookie-profile/{{rookie_id}}">{{rookie_full_name}}</a> exchanged for coupon #{{coupon_id}}',
            'lang' => 'EN',
            'description_rookie' => 'MOnthly Recurring GIft from {{leader_full_name}}'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');

        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('total_coupons_got');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('coupon_id');
        });
    }
}
