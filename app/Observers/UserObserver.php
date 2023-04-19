<?php

namespace App\Observers;

use App\Enums\UserEnum;
use App\Models\ProfileAlert;
use App\Models\ProfileAlertCode;
use App\Models\PubnubChannel;
use App\Models\RookieSeen;
use App\Models\Subscription;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param \App\Models\Subscription $subscription
     * @return void
     */
    public function updated(User $user)
    {
        if (!$user->wasChanged('status')) {
            return;
        }

        if($user->getOriginal('status')===$user->status){
            return;
        }

        $active = UserEnum::STATUS_ACTIVE_MAP[$user->status];
        $user->active = $active;
        $user->saveQuietly();

        if (!$active || $user->status==='fraud') {
            $field_to_find = $user->type . '_id';
            $subscription = Subscription::where($field_to_find, $user->id)->first();

            if(isset($subscription)){
                $subscription->update([
                    'status' => 'canceled', 'sent_reply_reminder_email_at' => null, 'canceled_at' => now(), 'deleted_at' => now(), 'valid_until_at' => now()
                ]);
                PubnubChannel::query()->where('subscription_id', $subscription->id)->update(['active' => false]);
            }

            RookieSeen::where($field_to_find, $user->id)->delete();
        }

        if($user->status==='blocked' && $user->type==='rookie'){
            RookieSeen::where('rookie_id', $user->id)->delete();
        }

        if($user->status==='accepted'){

            $pending_approval_code_id = ProfileAlertCode::query()->where('code', 'PA_ROOKIE_001')->first()->id;
            $pending_approval_alert = ProfileAlert::query()->where('user_id', $user->id)
                ->where('code_id', $pending_approval_code_id)->first();

            if(isset($pending_approval_alert)){
                $pending_approval_alert->delete();
                $profile_approved_code_id = ProfileAlertCode::query()->where('code', 'PA_ROOKIE_002')->first()->id;
                ProfileAlert::query()->create(['user_id' => $user->id, 'code_id' => $profile_approved_code_id]);
            }
        }
    }
}
