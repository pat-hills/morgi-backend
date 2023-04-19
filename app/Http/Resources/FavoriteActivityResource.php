<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\FavoriteActivitiesUsers; 

class FavoriteActivityResource extends Resource
{
    public function small(): FavoriteActivityResource
    {
        $this->attributes = [
            'id', 'name', 'key_name','users_favorite_activities_count'
        ];

        return $this;
    }

    public function regular(): FavoriteActivityResource
    {
        $this->small();
        return $this;
    }

    public function extended(): FavoriteActivityResource
    {
        $this->regular();
        return $this;
    }
}
