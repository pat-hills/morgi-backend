<?php

namespace App\Enums;

class UserEnum
{
    const TYPE_LEADER = 'leader';
    const TYPE_ROOKIE = 'rookie';
    const TYPE_OPERATOR = 'operator';
    const TYPE_ADMIN = 'admin';

    const STATUS_ACTIVE_MAP = [
        self::STATUS_PENDING => false,
        self::STATUS_ACCEPTED => true,
        self::STATUS_REJECTED => false,
        self::STATUS_UNTRUSTED => true,
        self::STATUS_BLOCKED => false,
        self::STATUS_NEW => false,
        self::STATUS_DELETED => false,
        self::STATUS_FRAUD => true
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_UNTRUSTED = 'untrusted';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_NEW = 'new';
    const STATUS_DELETED = 'deleted';
    const STATUS_FRAUD = 'fraud';

    const UNTRUSTED_STATUSES = [
        self::STATUS_PENDING => false,
        self::STATUS_ACCEPTED=> true,
        self::STATUS_REJECTED => false,
        self::STATUS_UNTRUSTED => true,
        self::STATUS_BLOCKED => false,
        self::STATUS_NEW => true,
        self::STATUS_DELETED => false,
        self::STATUS_FRAUD => false
    ];
}
