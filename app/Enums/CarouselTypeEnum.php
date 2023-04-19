<?php

namespace App\Enums;

class CarouselTypeEnum
{
    const HORIZONTAL = 'horizontal';
    const VERTICAL = 'vertical';
    const AB = 'a/b';

    const TYPES = [
        self::HORIZONTAL,
        self::VERTICAL,
        self::AB
    ];

    const TYPES_FILLABLE = [
        self::HORIZONTAL,
        self::VERTICAL,
    ];
}
