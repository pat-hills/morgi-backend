<?php

use App\Models\TransactionType;

test("Give bonus coupon to Leader", function () {

    $leader = \App\Models\Leader::query()
        ->select('leaders.*')
        ->join('leaders_ccbill_data', 'leaders.id', '=', 'leaders_ccbill_data.leader_id')
        ->join('users', 'users.id', '=', 'leaders.id')
        ->where('users.active', true)
        ->inRandomOrder()
        ->first();

    if(!isset($leader)){
        return;
    }

    $admin = \App\Models\User::query()->where('type', 'admin')->inRandomOrder()->first();
    $currency_value = 10;
    $reason = "Test reason";
    $admin_description = "Test description";
    $old_leader_total_coupons_got = $leader->total_coupons_got;

    $coupon_utils = new \App\Utils\CouponUtils($leader->id);

    $coupon = $coupon_utils->giveBonusCoupon($currency_value, $admin->id, $reason, $admin_description);

    $leader->refresh();

    /*
     * Check integrity of coupon obj
     */
    expect($coupon)->not->toBeNull()
        ->leader_id->toBe($leader->id)
        ->currency_value->toEqual($currency_value);

    $transaction = \App\Models\Transaction::find($coupon->from_transaction_id);
    $transaction_type = TransactionType::query()->where('type', 'bonus')->first();

    /*
     * Check integrity of transaction obj
     */
    expect($transaction)->not->toBeNull()
        ->leader_id->toBe($leader->id)
        ->coupon_id->toBe($coupon->id)
        ->morgi->toEqual($currency_value)
        ->type->toBe("bonus")
        ->transaction_type_id->toBe($transaction_type->id)
        ->referal_internal_id->toBeNull()
        ->rookie_id->toBeNull()
        ->admin_id->not->toBeNull()
        ->subscription_id->toBeNull()
        ->leader_payment_id->toBeNull()
        ->refund_type->toBeNull()
        ->user_block_id->toBeNull()
        ->refunded_at->toBeNull()
        ->refunded_by->toBeNull()
        ->payment_rookie_id->toBeNull()
        ->notes->toBe('Test reason')
        ->admin_description->toBe('Test description');


    /*
     * Check if total_coupons_got increment
     */
    expect($leader)->not->toBeNull()
        ->total_coupons_got->toBe($old_leader_total_coupons_got + 1)
        ->coupons->toBeArray();
});

test("Leader's coupon response", function () {

    $leader_without_coupons = \App\Models\Leader::query()
        ->where('total_coupons_got', 0)
        ->inRandomOrder()
        ->first();

    if(isset($leader_without_coupons)){
        expect($leader_without_coupons)->coupons->toBeEmpty();
    }

    $leader_with_coupons = \App\Models\Leader::query()->where('total_coupons_got', '>', 0)->inRandomOrder()->first();
    if(isset($leader_with_coupons)){

        $leader_coupons_response = $leader_with_coupons->coupons;
        if(isset($leader_coupons_response)){
            expect($leader_coupons_response)->toBeArray();

            foreach ($leader_coupons_response as $leader_coupon){
                expect($leader_coupon)->currency_value->toBeInt()->count->toBeInt();
            }
        }
    }
});

test("Leader's hasAvailableToken() response", function () {

    $random_coupon = \App\Models\Coupon::query()->where('is_spent', false)->inRandomOrder()->first();
    if(!isset($random_coupon)){
        return;
    }

    $leader = \App\Models\Leader::query()
        ->join('users', 'users.id', '=', 'leaders.id')
        ->where('users.active', true)
        ->where('leaders.id', $random_coupon->leader_id)
        ->first();
    if(!isset($leader)){
        return;
    }

    $leader_coupon = $leader->getAvailableCoupon($random_coupon->currency_value);

    expect($leader_coupon)->currency_value->toEqual($random_coupon->currency_value);
});

//TODO Implementare un check sulle row di transactions, notifications e subscriptions
test("Can Leader buy gift with coupon and at least 1 payment method", function () {

    $amount = 10;
    $subscription_package = \App\Models\SubscriptionPackage::query()->where('amount', $amount)->first();
    if(!isset($subscription_package)){
        return;
    }

    $leader = \App\Models\Leader::query()
        ->select('leaders.*')
        ->join('leaders_ccbill_data', 'leaders.id', '=', 'leaders_ccbill_data.leader_id')
        ->join('coupons', 'leaders.id', '=', 'coupons.leader_id')
        ->join('users', 'users.id', '=', 'leaders.id')
        ->where('users.active', true)
        ->where('coupons.is_spent', false)
        ->where('coupons.currency_value', $amount)
        ->where("leaders_ccbill_data.active", true)
        ->groupBy('leaders.id')
        ->inRandomOrder()
        ->first();
    if(!isset($leader)){
        return;
    }

    if(!$leader->canBuyMorgi($amount)){
        return;
    }

    $leader_user = \App\Models\User::find($leader->id);
    if(!isset($leader_user)){
        return;
    }

    $random_rookie = \App\Models\User::query()
        ->where('type', 'rookie')
        ->where('active', true)
        ->inRandomOrder()
        ->first();
    if(!isset($random_rookie)){
        return;
    }

    $response = $this->actingAs($leader_user, 'api')
        ->postJson("api/rookie/{$random_rookie->id}/gift", [
            'subscription_package_id' => $subscription_package->id
        ]);

    $response->assertStatus(200);
    //$response_content = $response->json();
});

//TODO Implementare un check sulle row di transactions, notifications e subscriptions
test("Can Leader buy gift with coupon and no payment method", function () {

    $amount = 10;
    $subscription_package = \App\Models\SubscriptionPackage::query()->where('amount', $amount)->first();
    if(!isset($subscription_package)){
        return;
    }

    $leader = \App\Models\Leader::query()
        ->select('leaders.*')
        ->leftJoin('leaders_ccbill_data', 'leaders.id', '=', 'leaders_ccbill_data.leader_id')
        ->join('coupons', 'leaders.id', '=', 'coupons.leader_id')
        ->join('users', 'users.id', '=', 'leaders.id')
        ->where('users.active', true)
        ->where('coupons.is_spent', false)
        ->where('coupons.currency_value', $amount)
        ->havingRaw('COUNT(leaders_ccbill_data.id)=0')
        ->inRandomOrder()
        ->first();
    if(!isset($leader)){
        return;
    }

    try {
        if(!$leader->canBuyMorgi($amount)){
            return;
        }
    }catch (Exception $exception){
        throw new Exception($exception->getMessage());
    }

    $leader_user = \App\Models\User::find($leader->id);
    if(!isset($leader_user)){
        return;
    }

    $random_rookie = \App\Models\User::query()
        ->where('type', 'rookie')
        ->where('active', true)
        ->inRandomOrder()
        ->first();
    if(!isset($random_rookie)){
        return;
    }

    $response = $this->actingAs($leader_user, 'api')
        ->postJson("api/rookie/{$random_rookie->id}/gift", [
            'subscription_package_id' => $subscription_package->id
        ]);

    $response->assertStatus(302);
    //$response_content = $response->json();
});
