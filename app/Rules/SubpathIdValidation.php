<?php

namespace App\Rules;

use App\Models\Path;
use Illuminate\Contracts\Validation\Rule;

class SubpathIdValidation implements Rule
{

    private $path_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($path_id)
    {
        $this->path_id = $path_id;
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
        $subpath = Path::query()->find($value);
        if(!$subpath || $subpath->is_subpath==false){
            return false;
        }

        $path = Path::query()->find($subpath->parent_id);
        if(!$path || $path->is_subpath==true || $path->id!=$this->path_id){
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
        return trans('auth.subpath_error');
    }
}
