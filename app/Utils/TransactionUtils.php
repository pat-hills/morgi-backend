<?php


namespace App\Utils;


use App\Models\Transaction;

class TransactionUtils
{
    public static function getTaxedAmountDollars($amount_dollars)
    {
        return ($amount_dollars*0.9)*0.75;
    }
}
