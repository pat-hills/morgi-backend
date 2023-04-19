<?php

namespace App\Rules;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Contracts\Validation\Rule;

class RegionIdValidation implements Rule
{

    private $country_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($country_id)
    {
        $this->country_id = $country_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $region = Region::query()->find($value);
        if(!$region || $region->country_id!=$this->country_id){
            return false;
        }

        $country = Country::query()->find($this->country_id);
        if(!$country || !$country->has_childs){
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('auth.region_error');
    }
}
