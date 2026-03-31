<?php
declare(strict_types=1);

namespace Core\Http;

use RuntimeException;
use Throwable;
use Core\Routing\Router;
use Core\Routing\RequestParser;
use Core\Routing\RouteConfig;
use Core\Logging\ErrorLogger;
use Core\Http\RequestData;

// === Dependencies ===
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/Core/Http/StatusHandler.php';
require_once __DIR__ . '/Core/Routing/RouteConfig.php';



// Example menu items
$menuItems = [
    ['link' => '/about', 'label' => 'About'],
    ['link' => '/contact', 'label' => 'Contact'],
    ['link' => '/home', 'label' => 'Home'],
];

// Example content
$content = '<h1>Welcome to the Application</h1>';

// Start output buffering
ob_start();

// Initialize the application
require_once __DIR__ . '/init.php';

try {
    // Parse the request URI
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $parsedRequest = RequestParser::parse($requestUri);

    // Extract parameters from the request
    $params = $parsedRequest['params'] ?? RouteConfig::getDefaultRoute();

    // Sanitize parameters
    $sanitizedParams = array_map('htmlspecialchars', $params);

    // Create a RequestData object
    $requestData = new RequestData($_SESSION ?? [], $sanitizedParams);

    // Resolve the route dynamically
    $routeFile = Router::resolveRoute($sanitizedParams);

    // Get the route content within a closure
    $content = (function(RequestData $requestData) use ($routeFile) {
        ob_start();
        require $routeFile;
        $output = ob_get_clean();
        return $output;
    })($requestData);

    // Clear any output buffering outside the closure
    ob_clean();

    // Output the content
    echo $content;
} catch (Throwable $e) {
    // Log the error
    ErrorLogger::log($e->getMessage());
    // Handle the error with a status response
    StatusHandler::handle(500, 'Internal Server Error');
}
