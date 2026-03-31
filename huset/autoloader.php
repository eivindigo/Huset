<?php

declare(strict_types=1);

/**
 * Simple PSR-4 Autoloader for classes and enums
 *
 * Usage:
 *   require_once __DIR__ . '/path/to/Autoloader.php';
 *   Autoloader::register(__DIR__ . '/..');
 *
 * This will automatically load classes/enums from the base directory using namespace and class name.
 */

namespace Core;

class Autoloader
{
    /**
     * Registers the autoloader with the SPL autoload stack.
     * @param string $baseDir The base directory for class files (default: current directory)
     */
    public static function register(string $baseDir = __DIR__): void
    {
        spl_autoload_register(function ($class) use ($baseDir) {
            // Convert namespace to file path
            $file = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (is_file($file)) {
                require_once $file;
            }
        });
    }
}
