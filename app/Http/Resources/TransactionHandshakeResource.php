<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\User;

class TransactionHandshakeResource extends Resource
{
    public function small(): TransactionHandshakeResource
    {
        $this->attributes = [
            'type',
            'status',
            'amount',
            'dollar_amount',
            'jpost_url',
            'subscription_id',
            'leader_payment_id',
            'has_active_payment_method'
        ];

        $this->addRookieToResource();

        return $this;
    }

    public function regular(): TransactionHandshakeResource
    {
        $this->small();
        return $this;
    }

    public function extended(): TransactionHandshakeResource
    {
        $this->regular();
        return $this;
    }

    private function addRookieToResource()
    {
        $rookies_ids = $this->resources->pluck('rookie_id');
        $rookies = UserResource::compute(
            $this->request,
            User::query()->whereIn('id', $rookies_ids)->get(),
            'small'
        )->get();

        foreach ($this->resources as $resource){
            $resource->rookie = $rookies->where('id', $resource->rookie_id)->first();
        }

        $this->attributes[] = 'rookie';
    }
}
