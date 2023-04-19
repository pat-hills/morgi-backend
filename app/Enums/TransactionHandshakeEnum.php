<?php

namespace App\Enums;

class TransactionHandshakeEnum
{
    const DEFAULT = 'default';
    const SUCCESS = 'success';
    const FAILURE = 'failure';

    const STATUSES = [
        self::DEFAULT,
        self::SUCCESS,
        self::FAILURE
    ];

    const MICROMORGI = 'micromorgi';
    const GIFT = 'gift';
    const RENEW = 'renew';
    const CREDIT_CARD = 'credit_card';

    const TYPES = [
        self::MICROMORGI,
        self::GIFT,
        self::RENEW,
        self::CREDIT_CARD
    ];
}
