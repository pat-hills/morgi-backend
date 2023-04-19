<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];

    private $replace_white_space = [
        'username'
    ];

    protected function transform($key, $value)
    {
        if(!is_string($value)){
            return $value;
        }

        if (in_array($key, $this->except, true)) {
            return $value;
        }

        $string = trim($value);

        if (in_array($key, $this->replace_white_space, true)) {
            $string = str_replace(' ', '', $string);
        }

        return $string;
    }
}
