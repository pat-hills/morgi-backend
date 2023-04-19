<?php

namespace App\Enums;

class TelegramMessageEnum
{

    const TEXT = 'text';
    const GIF = 'gif';

    const MEDIA_TYPES = [
        self::TEXT,
        self::GIF
    ];

    const INVALID_TOKEN = 'invalid_token';
    const DISCONNECT = 'disconnect';
    const WELCOME = 'welcome';
    const ALREADY_CONNECTED = 'already_connected';
    const FIRST_GIFT = 'first_gift';
    const RECURRING_GIFT = 'recurring_gift';
    const MICROMORGI_RECEIVED = 'micromorgi_received';

    const TYPES = [
        self::INVALID_TOKEN,
        self::DISCONNECT,
        self::WELCOME,
        self::ALREADY_CONNECTED,
        self::FIRST_GIFT,
        self::RECURRING_GIFT,
        self::MICROMORGI_RECEIVED
    ];
}
