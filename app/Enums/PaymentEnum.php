<?php

namespace App\Enums;

class PaymentEnum
{
    const NEW = 'new';
    const PENDING = 'pending';
    const COMPLETED = 'completed';

    const STATUS = [
        self::NEW,
        self::PENDING,
        self::COMPLETED
    ];
}
