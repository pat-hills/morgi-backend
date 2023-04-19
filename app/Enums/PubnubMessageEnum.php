<?php

namespace App\Enums;

class PubnubMessageEnum
{
    const MESSAGE = 'message';
    const MICROMORGI_TRANSACTION = 'micromorgi_transaction';
    const PHOTO = 'photo';
    const VIDEO = 'video';

    const TYPES = [
        self::MESSAGE,
        self::MICROMORGI_TRANSACTION,
        self::PHOTO,
        self::VIDEO
    ];

    const TYPE_MESSAGE = 'message';
    const TYPE_MICROMORGI_TRANSACTION = 'micromorgi_transaction';
    const TYPE_SYSTEM_BROADCAST = 'system_broadcast';
    const TYPE_PHOTO = 'photo';
    const TYPE_VIDEO = 'video';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_TEXT = 'text';

    const ATTRIBUTES = [
        'message' => [
            'type', 'text', 'user_id', 'sent_at'
        ],
        'micromorgi_transaction' => [
            'type', 'micromorgi_amount', 'user_id', 'sent_at'
        ],
        'photo' => [
            'type', 'url', 'user_id', 'sent_at'
        ],
        'video' => [
            'type', 'url', 'user_id', 'sent_at'
        ]
    ];
}
