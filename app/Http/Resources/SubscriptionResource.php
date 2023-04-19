<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Leader;
use App\Models\LeaderCcbillData;
use App\Models\User;

class SubscriptionResource extends Resource
{
    public function small(): SubscriptionResource
    {
        $this->addAttributes([
            'id',
            'amount',
            'status',
            'rookie_id',
            'leader_id',
            'ended_by',
            'is_recurring',
            'canceled_at',
            'failed_at',
            'deleted_at',
            'subscription_at',
            'last_subscription_at',
            'valid_until_at',
            'next_donation_at',
        ]);

        $this->addLast4ToResources();
        $this->addRookieToResources();
        $this->addLeaderToResources();

        return $this;
    }

    public function extended(): SubscriptionResource
    {
        $this->small();
        return $this;
    }

    private function addLast4ToResources()
    {
        $user = $this->request->user();
        if(!isset($user)){
            return;
        }

        $ccbill_datas_ids = $this->resources->pluck('leader_payment_method_id');
        $ccbils_datas = LeaderCcbillData::query()->findMany($ccbill_datas_ids);

        foreach ($this->resources as $resource) {

            // We show last4 digits only if the requesting user is the leader owning card
            if($resource->leader_id !== $user->id){
                $resource->last4 = null;
                continue;
            }

            $ccbill_data = $ccbils_datas->where('id', $resource->leader_payment_method_id)->first();
            $resource->last4 = (isset($ccbill_data)) ? $ccbill_data->last4 : null;
        }

        $this->attributes[] = 'last4';
    }

    private function addLeaderToResources()
    {
        $leaders_ids = $this->resources->pluck('leader_id');
        $leaders = User::query()->findMany($leaders_ids);

        foreach ($this->resources as $resource) {

            $leader = $leaders->where('id', $resource->leader_id)->first();
            $leader_response = [
                'id' => $leader->id,
                'avatar' => $leader->getPublicAvatar(),
                'description' => $leader->description,
                'username' => $leader->username
            ];

            $resource->leader = $leader_response;
        }

        $this->attributes[] = 'leader';
    }


    private function addRookieToResources()
    {
        $rookies_ids = $this->resources->pluck('rookie_id');
        $rookies = User::query()->findMany($rookies_ids);

        foreach ($this->resources as $resource) {

            $rookie = $rookies->where('id', $resource->rookie_id)->first();
            $rookie_response = [
                'id' => $rookie->id,
                'avatar' => $rookie->getPublicAvatar(),
                'username' => $rookie->username,
                'full_name' => $rookie->full_name,
            ];

            $resource->rookie = $rookie_response;
        }

        $this->attributes[] = 'rookie';
    }
}
