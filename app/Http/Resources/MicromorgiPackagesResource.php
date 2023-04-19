<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;

class MicromorgiPackagesResource extends Resource
{
    public function small(): MicromorgiPackagesResource
    {
        $this->attributes = [
            'id', 'micromorgi_count', 'price'
        ];

        return $this;
    }

    public function regular(): MicromorgiPackagesResource
    {
        $this->small();
        return $this;
    }

    public function extended(): MicromorgiPackagesResource
    {
        $this->regular();
        return $this;
    }
}
