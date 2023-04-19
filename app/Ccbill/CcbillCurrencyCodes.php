<?php


namespace App\Ccbill;


class CcbillCurrencyCodes
{
    const CODES = [
        'GBP' => '826',
        'USD' => '840',
        'EUR' => '978'
    ];

    const CURRENCY = [
        '826' => 'GBP',
        '840' => 'USD',
        '978' => 'EUR'
    ];

    const SYMBOLS = [
        'GBP' => '£',
        'USD' => '$',
        'EUR' => '€'
    ];

    public static function getCurrencyCode($currency)
    {
        /*
         * Default return in dev envs USD cause ccbill's bad
         */
        if(!in_array(env('APP_ENV'), ['prod', 'production'])){
            return '840';
        }

        return self::CODES[$currency];
    }

    public static function getCurrencySymbol($currency_code)
    {
        return self::SYMBOLS[self::CURRENCY[$currency_code]];
    }
}
