<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


class CountryResource extends Resource
{

    public function small(): CountryResource
    {
        $this->attributes = [
            'id', 'name', 'has_childs', 'alpha_3_code', 'alpha_2_code', 'timezone', 'currency', 'dial'
        ];

        return $this;
    }

    public function regular(): CountryResource
    {
        $this->small();
        return $this;
    }

    public function extended(): CountryResource
    {
        $this->regular();
        return $this;
    }
}
