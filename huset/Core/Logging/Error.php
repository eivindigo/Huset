<?php

declare(strict_types=1);

namespace Core\Logging;

use Throwable;

class ErrorLogger
{
    private const LOG_FILE = __DIR__ . '/../../../logs/router_errors.log';

    /**
     * Logs a router error to the log file.
     *
     * @param string $message Error description
     * @param Throwable|null $e The exception, if any
     */
    public static function log(string $message, ?Throwable $e = null): void
    {
        $logDir = dirname(self::LOG_FILE);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0750, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $detail = $e !== null ? " | {$e->getMessage()}" : '';
        $entry = "[{$timestamp}] {$message}{$detail}" . PHP_EOL;

        @file_put_contents(self::LOG_FILE, $entry, FILE_APPEND | LOCK_EX);
    }
}