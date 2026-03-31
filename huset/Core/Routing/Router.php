<?php

declare(strict_types=1);

namespace Core\Routing;

use RuntimeException;

class Router
{
    public static function resolveRoute(array $params): string
    {
        $baseDir = self::getBaseDir();

        // Ensure at least two parameters for folders and one for the file
        if (count($params) < 3) {
            throw new RuntimeException('Invalid route structure. At least two folders and one file are required.');
        }

        // Build the path dynamically
        $folder1 = $params[0];
        $folder2 = $params[1];
        $file = $params[2];

        $path = $baseDir . $folder1 . DIRECTORY_SEPARATOR . $folder2 . DIRECTORY_SEPARATOR . $file . '.php';

        // Validate the path
        return RouteValidator::validateRoutePath($path);
    }

    private static function getBaseDir(): string
    {
        return __DIR__ . '/../../routes/';
    }
}