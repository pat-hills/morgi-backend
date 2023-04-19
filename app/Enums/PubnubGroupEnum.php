<?php

namespace App\Enums;

class PubnubGroupEnum
{
    const DIRECT_CATEGORY = 'direct';

    const CATEGORIES = [
        self::DIRECT_CATEGORY
    ];

    const CHANNEL_GROUPS_PERMISSIONS = [
        'get' => true,
        'read' => true,
        'write' => true,
        'delete' => true,
        'update' => true,
        'manage' => true,
        'join' => true
    ];
}
