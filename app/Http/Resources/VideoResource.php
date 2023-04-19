<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Rookie;
use App\Models\User;
use App\Utils\StorageUtils;

class VideoResource extends Resource
{
    public function small(): VideoResource
    {
        $this->attributes = [
            'id', 'user_id', 'url', 'path_location', 'is_processed', 'created_at'
        ];

        $this->addUnderValidationToResource();

        return $this;
    }

    public function regular(): VideoResource
    {
        $this->small();
        return $this;
    }

    public function extended(): VideoResource
    {
        $this->regular();
        return $this;
    }

    private function addUnderValidationToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->under_validation = false;
        }

        $this->attributes[] = 'under_validation';
    }
}
