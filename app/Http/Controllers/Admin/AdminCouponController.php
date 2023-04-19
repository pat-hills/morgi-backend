<?php

namespace App\Http\Controllers\Admin;

use App\Models\Leader;
use App\Models\LeaderCcbillData;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Utils\CouponUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;

class  AdminCouponController {

    public function viewLeaderCoupons(User $user)
    {
        return view('admin.admin-pages.user_profile.leader.leader_coupon', compact('user'));
    }

    public function storeBonusCoupon(Leader $leader, Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_packages_id' => ['required', 'exists:subscription_packages,id'],
            'reason' => ['required']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $leader_ccbill_data_exists = LeaderCcbillData::query()
            ->where('leader_id', $leader->id)
            ->exists();

        if (!$leader_ccbill_data_exists) {
            return redirect()->back()->with(['fail' => "Unable to give bonus coupon to the leader. The leader has not payment method"]);
        }

        $amount = SubscriptionPackage::find($request->subscription_packages_id)->amount;

        DB::beginTransaction();
        try {
            $coupon_utils = new CouponUtils($leader->id);
            $coupon_utils->giveBonusCoupon($amount, Auth::id(), $request->reason, $request->comment);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with(['fail' => $exception->getMessage()]);
        }

        return redirect()->back()->with(['success' => 'Coupon added successfully']);
    }
}
