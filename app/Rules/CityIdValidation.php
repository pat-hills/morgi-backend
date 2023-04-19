<?php

namespace App\Rules;

use App\Models\City;
use App\Models\Country;
use Illuminate\Contracts\Validation\Rule;

class CityIdValidation implements Rule
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
        $country = Country::find($this->country_id);
        if(!$country){
            return false;
        }

        $city = City::find($value);
        if(!$city || $city->country_id!=$country->id){
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
        return trans('auth.city_error');
    }
}
