<?php

namespace App\Enums;

class SendGridEmailCheckEnum
{
    const INVALID_EMAIL = 'invalid_email';
    const BLOCK = 'block';
    const SPAM_REPORT = 'spam_report';
    const BOUNCE = 'bounce';

    const TYPE = [
        self::INVALID_EMAIL,
        self::BLOCK,
        self::SPAM_REPORT,
        self::BOUNCE
    ];
}
