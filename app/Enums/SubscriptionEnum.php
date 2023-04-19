<?php

namespace App\Enums;

class SubscriptionEnum
{
    const TYPE_FREE = 'free';
    const TYPE_PAID = 'paid';
    const STATUS_ACTIVE = 'active';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_UNSUFFICENT_FUNDS = 'unsufficent_funds'; //TODO: rename unsufficent_funds to insufficient_funds. Code and Database
    const STATUS_PAUSED = 'paused';
    const STATUS_PENDING = 'pending';

    public const STATUS_NOT_ACTIVE = [
        self::STATUS_FAILED,
        self::STATUS_CANCELED,
        self::STATUS_UNSUFFICENT_FUNDS
    ];
}
