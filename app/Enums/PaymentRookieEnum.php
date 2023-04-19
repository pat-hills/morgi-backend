<?php

namespace App\Enums;

class PaymentRookieEnum
{
    const PENDING = 'pending';
    const SUCCESSFUL = 'successful';
    const DECLINED = 'declined';
    const RETURNED = 'returned';

    const STATUS = [
        self::PENDING,
        self::SUCCESSFUL,
        self::DECLINED,
        self::RETURNED
    ];
}
