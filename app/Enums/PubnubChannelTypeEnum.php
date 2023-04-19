<?php

namespace App\Enums;

class PubnubChannelTypeEnum
{
    const FREE_CONNECTION = 'free_connection';

    const REFERRAL = 'referral';

    const SUBSCRIPTION = 'subscription';

    const TYPES = [
        self::FREE_CONNECTION,
        self::REFERRAL,
        self::SUBSCRIPTION
    ];
}
