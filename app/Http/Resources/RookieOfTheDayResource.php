<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\User;

class RookieOfTheDayResource extends Resource
{
    public function small(): RookieOfTheDayResource
    {
        $this->attributes = [
            'morgi',
            'micro_morgi',
            'score',
            'max_score'
        ];

        $this->addRookieToResource();

        return $this;
    }

    public function regular(): RookieOfTheDayResource
    {
        $this->small();
        return $this;
    }

    public function extended(): RookieOfTheDayResource
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
