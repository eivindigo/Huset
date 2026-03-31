<?php

require_once __DIR__ . '/autoloader.php';
\Core\Autoloader::register(__DIR__);
use Core\Http\StatusCode;


// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy - add trusted sources here as needed
$csp = [
    'default-src' => ["'self'"],
    'script-src'  => ["'self'"],
    'style-src'   => ["'self'", 'https://fonts.googleapis.com'],
    'font-src'    => ["'self'", 'https://fonts.gstatic.com'],
    'connect-src' => ["'self'"],
    'img-src'     => ["'self'"],
];

$policy = implode('; ', array_map(
    fn (string $key, array $sources) => $key . ' ' . implode(' ', $sources),
    array_keys($csp),
    array_values($csp)
));

header("Content-Security-Policy: {$policy}");

// Check if a session is already active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    regenerateCsrfToken();
}

function validateCsrfToken(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';

        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            error_log('CSRF token validation failed for request from IP: ' . $_SERVER['REMOTE_ADDR']); // Log the event
            http_response_code(400); // Set the HTTP response code to 400 Bad Request
            die('Ugyldig forespørsel');
        }
    }
}

function regenerateCsrfToken(): string {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

// Add meta refresh header for 2 seconds