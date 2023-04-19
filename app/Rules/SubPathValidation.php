<?php

namespace App\Rules;

use App\Models\Path;
use Illuminate\Contracts\Validation\Rule;

class SubPathValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $subpath = Path::where('name', $value)->where('is_subpath', true)->first();
        if($subpath){
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
        return 'The subpath has already been taken.';
    }
}
