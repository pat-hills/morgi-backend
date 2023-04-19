<?php

namespace App\Services\Sentry;

use Exception;

class SentryLogger
{
    public static function logMessage(string $message): void
    {
        \Sentry\captureMessage($message);
    }

    public static function logException(Exception $exception): void
    {
        \Sentry\captureException($exception);
    }
}
