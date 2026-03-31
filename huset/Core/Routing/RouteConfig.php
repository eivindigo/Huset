<?php

declare(strict_types=1);

namespace Core\Routing;

class RouteConfig
{
    const ROUTES_BASE_DIR = __DIR__ . '/../../routes/';

    const DEFAULT_ROUTE = ['frontpage', 'index'];

    public static function getDefaultRoute(): array
    {
        return self::DEFAULT_ROUTE;
    }
}