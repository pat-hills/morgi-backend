<?php

namespace App\Rules;

use App\Models\EmailValidated;
use App\Sendgrid\SendgridAPI;
use App\Utils\EmailBlacklist\EmailBlacklistUtils;
use Illuminate\Contracts\Validation\Rule;

class EmailValidation implements Rule
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
        if(EmailBlacklistUtils::set($value)->isBlacklisted()){
            return false;
        }

        if(env('APP_ENV')==='prod') {

            $email_validated = EmailValidated::where('email', $value)->first();
            if (!isset($email_validated)) {

                $sendgrid_validation = new SendgridAPI('email_validation');
                $validation_response = $sendgrid_validation->emailValidation($value, 'SIGNUP');

                return $validation_response['status'] === true;
            }

            return $email_validated->verdict==='valid';
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
        return 'Your email is invalid, please write a new one';
    }
}
