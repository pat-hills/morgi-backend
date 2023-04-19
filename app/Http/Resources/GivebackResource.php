<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;

class GivebackResource extends Resource
{
    public function small(): GivebackResource
    {
        $this->attributes = [
            'id', 'total_subscriptions_count', 'micromorgi'
        ];

        return $this;
    }

    public function regular(): GivebackResource
    {
        $this->small();
        return $this;
    }

    public function extended(): GivebackResource
    {
        $this->regular();
        return $this;
    }
}
