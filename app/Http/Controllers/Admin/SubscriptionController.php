<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionEnum;
use App\Enums\UserEnum;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Leader;
use App\Models\LeaderPayment;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    public function endSubscription(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => ['required', 'exists:subscriptions,id']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $subscription = Subscription::query()->find($request->subscription_id);
        if($subscription->deleted_at){
            return redirect()->back()->with(['fail' => 'Subscription already ended']);
        }

        $leader = Leader::query()->find($subscription->leader_id);
        if (!isset($leader)){
            return redirect()->back()->with(['fail' => 'Something went wrong']);
        }

        try {
            $leader->endSubscription($subscription->rookie_id, Auth::id());
        }catch (\Exception $exception) {
            return redirect()->back()->with(['fail' => $exception->getMessage()]);
        }

        return redirect()->back()->with(['success' => "Subscription ended by admin"]);
    }

    public function showSubscriptions($id)
    {
        $data = ['user_id' => $id];
        $validator = Validator::make($data, ['user_id' => ['required', 'integer']]);

        if ($validator->fails()) {
            return \redirect()->back()->with(['fail' => $validator->errors()->messages()], Response::HTTP_BAD_REQUEST);
        }

        $endpoint = request()->segment(count(request()->segments()));
        if(!in_array($endpoint, ['active_gifts', 'not_active_gifts'])){
            return \redirect()->back()->with(['fail' => 'Invalid Page'], Response::HTTP_NOT_FOUND);
        }

        try {
            $user = User::query()
                ->where('id', $id)
                ->whereIn('type', [UserEnum::TYPE_LEADER, UserEnum::TYPE_ROOKIE])
                ->firstOrFail();
        }catch (ModelNotFoundException $exception){
            return \redirect()->back()->with(['fail' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $subscription_query = Subscription::query();

        $subscription_query->when($user->type === 'rookie', function ($query) use ($user){
            $query->where('subscriptions.rookie_id', $user->id);
        });

        $subscription_query->when($user->type === 'leader', function ($query) use ($user){
            $query->where('subscriptions.leader_id', $user->id);
        });

        $subscriptions_collection = $subscription_query->get();

        $subscriptions_ids = $subscriptions_collection->pluck('id');

        switch ($endpoint){
            case 'active_gifts':

                $subscriptions_result = $subscriptions_collection->where('status', SubscriptionEnum::STATUS_ACTIVE);
                $opposite_subscriptions_counter = $subscriptions_collection->whereIn('status', SubscriptionEnum::STATUS_NOT_ACTIVE)->count();
                $path_view = 'admin.admin-pages.user_profile.' . $user->type . '.' . $user->type . '_monthly-active-gifts';

                break;
            case 'not_active_gifts':

                $subscriptions_result = $subscriptions_collection->whereIn('status', SubscriptionEnum::STATUS_NOT_ACTIVE);
                $opposite_subscriptions_counter = $subscriptions_collection->where('status', SubscriptionEnum::STATUS_ACTIVE)->count();
                $path_view = 'admin.admin-pages.user_profile.' . $user->type . '.' . $user->type . '_monthly-not-active-gifts';

                break;
        }

        $total_monthly = $subscriptions_result->sum('amount');
        $leaders_payments_collection = LeaderPayment::query()
            ->whereIn('subscription_id', $subscriptions_ids)
            ->get()
            ->groupBy('subscription_id');

        $transactions_collection = Transaction::query()
            ->whereIn('subscription_id', $subscriptions_ids)
            ->whereNotNull('coupon_id')
            ->get();

        $coupons_collection = Coupon::query()
            ->whereIn('to_transaction_id', $transactions_collection->pluck('id'))
            ->get();

        $external_users_query = User::query();

        $external_users_query->when($user->type === 'rookie', function ($query) use ($subscriptions_collection){
            $query->whereIn('id', $subscriptions_collection->pluck('leader_id'));
        });

        $external_users_query->when($user->type === 'leader', function ($query) use ($subscriptions_collection){
            $query->whereIn('id', $subscriptions_collection->pluck('rookie_id'));
        });

        $external_users_collection = $external_users_query->get();
        $external_field_user_type = ($user->type === 'rookie')
            ? 'leader_id'
            : 'rookie_id';

        $subscriptions = [];

        $all_transactions_collection = Transaction::query()
            ->whereIn('subscription_id', $subscriptions_result->pluck('id'))
            ->get();

        foreach ($subscriptions_result as $subscription){

            $transaction = $all_transactions_collection->where('subscription_id', $subscription->id)->last();

            $leader_payment = ($leaders_payments_collection->has($subscription->id))
                ? $leaders_payments_collection->get($subscription->id)->last()
                : null;

            $status = (isset($leader_payment))
                ? $leader_payment->status
                : null;

            if(isset($transaction)){

                $coupon = (isset($transaction->coupon_id))
                    ? $coupons_collection->where('to_transaction_id', $transaction->id)->first()
                    : null;

                $status = (isset($coupon))
                    ? "COUPON"
                    : $status;
            }

            $external_user = $external_users_collection->where('id', $subscription->$external_field_user_type)->first();

            $subscriptions[] = [
                'id' => $subscription->id,
                'leader_payment_id' => $subscription->leader_payment_id,
                'username' => $external_user->full_name,
                'username_id' => $external_user->id,
                'email' => $external_user->email,
                'last_subscription_at' => date('y-M-d, h:i A', strtotime($subscription->last_subscription_at)),
                'canceled_at' => ($subscription->canceled_at) ? date('y-M-d, h:i A', strtotime($subscription->canceled_at)) : null,
                'status' => $status,
                'morgi' => $subscription->amount
            ];
        }

        return view($path_view, compact('user', 'subscriptions', 'opposite_subscriptions_counter', 'total_monthly'));
    }

}
