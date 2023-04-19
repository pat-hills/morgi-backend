<?php

namespace App\Utils\Subscription\Create;

use App\Ccbill\CcbillUtils;
use App\Models\Leader;
use App\Models\Photo;
use App\Models\Rookie;
use App\Models\User;
use App\Transactions\Morgi\TransactionGift;
use App\Utils\FreeConnectionChannelUtils;
use App\Utils\Utils;
use Illuminate\Http\Request;

class PaidSubscriptionCreateUtils
{
    public static function handle(Request $request, Leader $leader, Rookie $rookie): ?string
    {
        try {
            $photo_id = (isset($request->photo_id))
                ? Photo::query()->where('id', $request->photo_id)->where('user_id', $rookie->id)->first()->id ?? null
                : null;

            $ip_address = Utils::getRealIp($request);

            $result = $leader->attemptPaymentWithPaymentMethods($request->amount, null, $ip_address);
            if($result['status']===false){
                return CcbillUtils::jpostSubscription($leader, $rookie->id, $request->amount, $rookie->first_name);
            }

            $subscription_utils = SubscriptionCreateUtils::configure($leader->id, $rookie->id, $request->amount);

            if(isset($photo_id)){
                $subscription_utils->setPhotoId($photo_id);
            }

            $paused_channel_exists = FreeConnectionChannelUtils::pausedChannelExists($leader->id, $rookie->id);
            $subscription = $subscription_utils->setLeaderPaymentMethodId($result['payment_method_id'])->create();

            TransactionGift::create(
                $subscription->rookie_id,
                $subscription->leader_id,
                $subscription->amount,
                $subscription->id,
                $result['payment_method_id'],
                $result['subscriptionId'],
                false,
                $ip_address
            );

            if($paused_channel_exists){
                User::find($rookie->id)->increment('total_successful_paused_connections');
                User::find($leader->id)->increment('total_successful_paused_connections');
            }

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage(), 400);
        }

        return null;
    }
}
