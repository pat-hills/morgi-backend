<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\User;

class PhotoHistoryResource extends Resource
{
    public function small(): PhotoHistoryResource
    {
        $this->attributes = [
            'id', 'user_id', 'url', 'path_location', 'main', 'created_at'
        ];

        $this->addUnderValidationToResource();

        return $this;
    }

    public function regular(): PhotoHistoryResource
    {
        $this->small();
        $this->attributes = array_merge($this->attributes, ['status', 'declined_reason']);

        return $this;
    }

    public function extended(): PhotoHistoryResource
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
