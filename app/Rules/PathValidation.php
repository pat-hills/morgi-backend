<?php

namespace App\Rules;

use App\Models\Path;
use Illuminate\Contracts\Validation\Rule;

class PathValidation implements Rule
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
        $path = Path::query()->find($value);
        return isset($path) && !$path->is_subpath;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid path provided';
    }
}
