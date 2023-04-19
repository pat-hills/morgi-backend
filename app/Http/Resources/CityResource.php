<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Country;
use Illuminate\Support\Collection;

class CityResource extends Resource
{

    public function small(): CityResource
    {
        $this->attributes = [
            'id', 'name', 'country_id'
        ];

        return $this;
    }

    public function regular(): CityResource
    {
        $this->small();
        return $this;
    }

    public function extended(): CityResource
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
