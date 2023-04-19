<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Country;

class RegionResource extends Resource
{
    public function small(): RegionResource
    {
        $this->attributes = [
            'id', 'name'
        ];

        return $this;
    }

    public function regular(): RegionResource
    {
        $this->small();
        $this->attributes = array_merge($this->attributes, ['alpha_2_code', 'country_id']);

        return $this;
    }

    public function extended(): RegionResource
    {
        $this->regular();

        $this->addCountryToResource();

        return $this;
    }

    private function addCountryToResource()
    {
        $countries_ids = $this->resources->pluck('country_id');
        $countries = CountryResource::compute(
            $this->request,
            Country::query()->whereIn('id', $countries_ids)->get(),
            'small'
        )->get();

        foreach ($this->resources as $resource){
            $resource->country = $countries->where('id', $resource->country_id)->first();
        }

        $this->attributes[] = 'country';
    }
}
