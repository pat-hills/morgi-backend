<?php

namespace App\Enums;

class TransactionRefundEnum
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_FAILED = 'failed';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_FAILED
    ];
}
