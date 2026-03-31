// Added logRouterError function to ErrorLogger class.

namespace Core\Logging;

use Throwable;

class ErrorLogger
{
    public static function logRouterError(string $message, ?Throwable $e = null): void
    {
        $logDir = dirname(ERROR_LOG_FILE);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0750, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $detail = $e !== null ? " | {$e->getMessage()}" : '';
        $entry = "[{$timestamp}] {$message}{$detail}" . PHP_EOL;

        @file_put_contents(ERROR_LOG_FILE, $entry, FILE_APPEND | LOCK_EX);
    }

    public static function log(Throwable $e): void
    {
        self::logRouterError('Unhandled exception', $e);
    }
}