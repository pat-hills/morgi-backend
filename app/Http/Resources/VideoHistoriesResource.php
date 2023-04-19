<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Rookie;
use App\Models\User;
use App\Utils\StorageUtils;

class VideoHistoriesResource extends Resource
{
    public function small(): VideoHistoriesResource
    {
        $this->attributes = [
            'id', 'user_id', 'url', 'path_location', 'main', 'created_at'
        ];

        $this->addUnderValidationToResource();

        return $this;
    }

    public function regular(): VideoHistoriesResource
    {
        $this->small();
        $this->attributes = array_merge($this->attributes, ['status', 'declined_reason']);

        return $this;
    }

    public function extended(): VideoHistoriesResource
    {
        $this->regular();
        return $this;
    }

    private function addUnderValidationToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->under_validation = true;
        }

        $this->attributes[] = 'under_validation';
    }
}
