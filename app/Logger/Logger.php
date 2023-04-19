<?php

namespace App\Logger;

use App\Services\Sentry\SentryLogger;

class Logger
{
    public static function logMessage(string $message): void
    {
        SentryLogger::logMessage($message);
    }

    public static function logException(\Exception $exception): void
    {
        SentryLogger::logException($exception);
    }
}
