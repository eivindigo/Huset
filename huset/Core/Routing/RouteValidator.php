<?php

declare(strict_types=1);

namespace Core\Routing;

use RuntimeException;

class RouteValidator
{
    private const ROUTES_BASE_DIR = __DIR__ . '/../../routes/';

    public static function validateRoutePath(string $filePath): string
    {
        $routesRealPath = realpath(self::ROUTES_BASE_DIR);
        if ($routesRealPath === false) {
            throw new RuntimeException('Routes directory does not exist');
        }

        $fileRealPath = realpath($filePath);
        if ($fileRealPath === false) {
            throw new RuntimeException('Route file does not exist');
        }

        if (!str_starts_with($fileRealPath, $routesRealPath . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('Route file is outside the allowed directory');
        }

        return $fileRealPath;
    }
}