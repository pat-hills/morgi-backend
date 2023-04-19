<?php


namespace App\Ccbill;


class CcbillApiErrorCodes
{
    const CODES = [
        0 => 'The requested action failed.',
        -1 => 'The arguments provided to authenticate the merchant were invalid or missing.',
        -2 => 'The subscription id provided was invalid or the subscription type is not supported by the requested action.',
        -3 => 'No record was found for the given subscription.',
        -4 => 'The given subscription was not for the account the merchant was authenticated on.',
        -5 => 'The arguments provided for the requested action were invalid or missing.',
        -6 => 'The requested action was invalid',
        -7 => 'There was an internal error or a database error and the requested action could not complete.',
        -8 => 'The IP Address the merchant was attempting to authenticate on was not in the valid range.',
        -9 => 'The merchant’s account has been deactivated for use on the Datalink system or the merchant is not permitted to perform the requested action',
        -10 => 'The merchant has not been set up to use the Datalink system.',
        -11 => 'Subscription is not eligible for a discount, recurring price less than $5.00.',
        -12 => 'The merchant has unsuccessfully logged into the system 3 or more times in the last hour.',
        -15 => 'Merchant over refund threshold',
        -16 => 'Merchant over void threshold',
        -23 => 'Transaction limit reached',
        -24 => 'Purchase limit reached',
    ];

    public static function getError($error_code)
    {
        if(!isset(self::CODES[$error_code])){
            return null;
        }

        return ['code' => $error_code, 'description' => self::CODES[$error_code]];
    }
}
