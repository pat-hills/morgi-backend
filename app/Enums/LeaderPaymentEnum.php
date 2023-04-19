<?php

namespace App\Enums;

class LeaderPaymentEnum
{
    const STATUS_PAID = 'paid';
    const STATUS_REFUNDED = 'refunded';

    const IN_PROGRESS = 'in_progress';
    const FAILED = 'failed';
    const FAILED_ATTEMPT = 'failed_attempt';
    const PAID = 'paid';
    const TO_REFUND = 'to_refund';
    const REFUND_IN_PROGRESS = 'refund_in_progress';
    const REFUNDED = 'refunded';
    const ERROR_TO_REFUND = 'error_to_refund';

    const STATUS = [
        self::IN_PROGRESS,
        self::FAILED,
        self::FAILED_ATTEMPT,
        self::PAID,
        self::TO_REFUND,
        self::REFUND_IN_PROGRESS,
        self::REFUNDED,
        self::ERROR_TO_REFUND
    ];

    const MM_PURCHASE = 'mm_purchase';
    const REBILL = 'rebill';
    const FIRST_PURCHASE = 'first_purchase';

    const TYPE = [
        self::MM_PURCHASE,
        self::REBILL,
        self::FIRST_PURCHASE
    ];

    const MORGI = 'morgi';
    const MICRO_MORGI = 'micro_morgi';

    const CURRENCY_TYPE = [
        self::MORGI,
        self::MICRO_MORGI,
    ];


}
