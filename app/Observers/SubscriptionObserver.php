<?php

namespace App\Observers;

use App\Models\Leader;
use App\Models\PubnubChannel;
use App\Models\Subscription;
use App\Models\UserPath;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "updated" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function updated(Subscription $subscription)
    {
        if($subscription->wasChanged('status') && $subscription->getOriginal('status') !== 'active' && $subscription->status === 'active') {

            PubnubChannel::where('subscription_id', $subscription->id)->update(['active' => true]);
            $leader = Leader::query()->find($subscription->leader_id);
            $rookie_path_id = UserPath::where('user_id', $subscription->rookie_id)->where('is_subpath', false)->first()->path_id;

            if(!$leader->hasPath($rookie_path_id)){
                $leader->unlockPath($rookie_path_id);
            }
        }
    }
}
