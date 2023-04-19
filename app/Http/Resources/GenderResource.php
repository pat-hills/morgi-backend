<?php
namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;

class GenderResource extends Resource
{
    public function small(): GenderResource
    {
        $this->attributes = [
            'id', 'name', 'key_name'
        ];

        return $this;
    }

    public function regular(): GenderResource
    {
        $this->small();
        return $this;
    }

    public function extended(): GenderResource
    {
        $this->regular();
        return $this;
    }
}
