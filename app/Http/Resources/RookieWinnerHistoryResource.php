<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\User;

class RookieWinnerHistoryResource extends Resource
{
    public function small(): RookieWinnerHistoryResource
    {
        $this->attributes = [
            'id',
            'win_at',
        ];

        $this->addRookieToResource();

        return $this;
    }

    public function regular(): RookieWinnerHistoryResource
    {
        $this->small();
        return $this;
    }

    public function extended(): RookieWinnerHistoryResource
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
