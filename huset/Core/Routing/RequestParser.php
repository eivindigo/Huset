<?php

declare(strict_types=1);

namespace Core\Routing;

class RequestParser
{
    const MAX_PARAM_LENGTH = 64;

    public static function sanitizeSegment(string $segment): string
    {
        $clean = preg_replace('/[^A-Za-z0-9_\-]/', '', rawurldecode($segment));
        return substr($clean, 0, self::MAX_PARAM_LENGTH);
    }

    public static function parse(string $uri): array
    {
        $route = trim(parse_url($uri, PHP_URL_PATH), '/');
        $segments = $route === '' ? [] : explode('/', $route);

        $routeName = self::sanitizeSegment($segments[0] ?? '');
        $routeParams = array_map(
            fn (string $segment) => self::sanitizeSegment($segment),
            array_slice($segments, 1)
        );

        return [$routeName, $routeParams];
    }
}