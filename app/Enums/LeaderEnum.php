<?php

namespace App\Enums;

class LeaderEnum
{
    const FORCE_STATUS_BY_ADMIN = [
        'suspend' => 'SUSPEND',
        'under_review' => 'UNDER REVIEW',
        'fraud' => 'FRAUD'
    ];

    const EUR = 'EUR';
    const USD = 'USD';
    const GBP = 'GBP';

    const CURRENCY = [
        self::EUR,
        self::USD,
        self::GBP
    ];

}
