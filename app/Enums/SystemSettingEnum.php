<?php

namespace App\Enums;

class SystemSettingEnum
{
    const CONVERTERS_CAROUSEL_ORDER_RANDOMLY = 'randomly';
    const CONVERTERS_CAROUSEL_ORDER_CUSTOM = 'custom';

    const CONVERTERS_CAROUSEL_ORDERS_AVAILABLE = [
        self::CONVERTERS_CAROUSEL_ORDER_RANDOMLY,
        self::CONVERTERS_CAROUSEL_ORDER_CUSTOM
    ];
}
