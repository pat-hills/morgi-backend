<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;

class PubnubGroupResource extends Resource
{
    public function small(): PubnubGroupResource
    {
        $this->attributes = [
            'id',
            'name',
            'user_id'
        ];

        return $this;
    }
}
