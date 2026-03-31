<?php

namespace Core\Http;

enum StatusCode: int
{
    // 4xx Client Errors
    case BadRequest = 400;
    case Unauthorized = 401;
    case PaymentRequired = 402;
    case Forbidden = 403;
    case PageNotFound = 404;
    case MethodNotAllowed = 405;
    case NotAcceptable = 406;
    case ProxyAuthenticationRequired = 407;
    case RequestTimeout = 408;
    case Conflict = 409;
    case Gone = 410;
    case LengthRequired = 411;
    case PreconditionFailed = 412;
    case PayloadTooLarge = 413;
    case UriTooLong = 414;
    case UnsupportedMediaType = 415;
    case RangeNotSatisfiable = 416;
    case ExpectationFailed = 417;
    case ImATeapot = 418;
    case MisdirectedRequest = 421;
    case UnprocessableEntity = 422;
    case Locked = 423;
    case FailedDependency = 424;
    case TooEarly = 425;
    case UpgradeRequired = 426;
    case PreconditionRequired = 428;
    case TooManyRequests = 429;
    case RequestHeaderFieldsTooLarge = 431;
    case UnavailableForLegalReasons = 451;

    // 5xx Server Errors
    case InternalServerError = 500;
    case NotImplemented = 501;
    case BadGateway = 502;
    case ServiceUnavailable = 503;
    case GatewayTimeout = 504;
    case HttpVersionNotSupported = 505;
    case VariantAlsoNegotiates = 506;
    case InsufficientStorage = 507;
    case LoopDetected = 508;
    case NotExtended = 510;
    case NetworkAuthenticationRequired = 511;

    /**
     * Get a message field from StatusMessages.json.
     *
     * @param string $lang 'en' or 'no'
     * @param string $field 'title' or 'description'
     */
    private function message(string $lang, string $field): string
    {
        static $messages = null;
        $messages ??= json_decode(
            file_get_contents(__DIR__ . '/StatusMessages.json'),
            true
        );

        return $messages[(string) $this->value][$lang][$field]
            ?? "{$this->value} Unknown Status";
    }

    public function title(): string
    {
        return $this->message('en', 'title');
    }

    public function titleNo(): string
    {
        return $this->message('no', 'title');
    }

    public function description(): string
    {
        return $this->message('en', 'description');
    }

    public function descriptionNo(): string
    {
        return $this->message('no', 'description');
    }
}

/**
 * Send a complete error page for the given status code and terminate output.
 */
function sendHttpStatus(StatusCode $status): void
{
    $code = $status->value;
    $titleNo = htmlspecialchars("{$code} {$status->titleNo()}", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $descNo = htmlspecialchars($status->descriptionNo(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $title = htmlspecialchars("{$code} {$status->title()}", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $desc = htmlspecialchars($status->description(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    http_response_code($code);

    $extraStyle = '';
    $extraHtml = '';

    if ($status === StatusCode::PageNotFound) {
        $extraStyle = '    .code { background: #f1f5f9; padding: 0.2rem 0.4rem; border-radius: 0.25rem; }';
        $extraHtml = <<<'HTML'
    <p>Gå tilbake til <a href="/">forsiden</a> eller sjekk nettadressen for feil.</p>
    <p>Try going back to <a href="/">home</a> or check the URL for mistakes.</p>
    <p class="code">Requested path: <span id="path"></span></p>
  </div>
  <script>
    document.getElementById('path').textContent = location.pathname + location.search;
  </script>
HTML;
    } else {
        $extraHtml = "    <p><a href=\"/\">Gå til forsiden</a></p>\n    <p><a href=\"/\">Return home</a></p>";
    }

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{$title}</title>
  <style>
    body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #f5f7fb; color: #0f172a; margin: 0; }
    .container { max-width: 720px; margin: 8vh auto; padding: 0 1.5rem; }
    h1 { font-size: 3rem; margin: 0 0 0.5rem; }
    p { margin: 0 0 1rem; color: #475569; }
    a { color: #ea580c; text-decoration: none; }
    a:hover { text-decoration: underline; }
{$extraStyle}
  </style>
</head>
<body>
  <div class="container">
    <h1>{$titleNo}</h1>
    <p>{$descNo}</p>
    <hr style="margin:1.5rem 0;border:none;border-top:1px solid #e2e8f0">
    <h1>{$title}</h1>
    <p>{$desc}</p>
{$extraHtml}
  </div>
</body>
</html>
HTML;
}

class StatusHandler
{
    public static function handle(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        echo "<h1>{$statusCode}</h1><p>{$message}</p>";
    }
}
