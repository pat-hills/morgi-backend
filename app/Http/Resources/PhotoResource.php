<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\User;

class PhotoResource extends Resource
{
    public function small(): PhotoResource
    {
        $this->attributes = [
            'id', 'user_id', 'url', 'path_location', 'main', 'created_at'
        ];

        $this->addUnderValidationToResource();

        return $this;
    }

    public function regular(): PhotoResource
    {
        $this->small();
        return $this;
    }

    public function extended(): PhotoResource
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
