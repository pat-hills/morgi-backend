<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;

class CarouselSettingResource extends Resource
{
    public function small()
    {
        $this->addAttributes([
            'id',
            'type',
            'is_active'
        ]);

        return $this;
    }
}
