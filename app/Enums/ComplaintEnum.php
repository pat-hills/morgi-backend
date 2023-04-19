<?php

namespace App\Enums;

class ComplaintEnum
{
    const OPEN = 'open';
    const CLOSED = 'closed';

    const STATUS = [
        self::OPEN,
        self::CLOSED
    ];
}
