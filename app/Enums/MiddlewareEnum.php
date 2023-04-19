<?php

namespace App\Enums;

class MiddlewareEnum
{
    const MIDDLEWARE_AUTH = 'auth:api';
    const MIDDLEWARE_IS_ROOKIE = 'isRookie';
    const MIDDLEWARE_IS_LEADER = 'isLeader';
    const MIDDLEWARE_IS_CCBILL = 'isCcbill';
    const MIDDLEWARE_IS_UNTRUSTED = 'isUntrusted';
    const MIDDLEWARE_IS_ACTIVE = 'isActive';
    const MIDDLEWARE_IS_SENDGRID = 'isSendgrid';
    const MIDDLEWARE_UPDATE_LAST_ACTIVITY_AT = 'updateLastActivityAt';

    const BASE_MIDDLEWARE = [
        MiddlewareEnum::MIDDLEWARE_AUTH,
        MiddlewareEnum::MIDDLEWARE_IS_UNTRUSTED,
        MiddlewareEnum::MIDDLEWARE_UPDATE_LAST_ACTIVITY_AT,
        MiddlewareEnum::MIDDLEWARE_IS_ACTIVE
    ];
}
