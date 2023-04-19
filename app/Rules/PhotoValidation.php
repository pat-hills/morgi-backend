<?php

namespace App\Rules;

use App\Models\Photo;
use App\Models\PhotoHistory;
use Illuminate\Contracts\Validation\Rule;

class PhotoValidation implements Rule
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
        $photo_history = PhotoHistory::where('path_location', $value)->first();
        if($photo_history){
            return false;
        }

        $photo = Photo::where('path_location', $value)->first();
        if($photo){
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
        return 'Invalid profile picture provided';
    }
}
