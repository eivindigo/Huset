<?php

declare(strict_types=1);

namespace Core\Http;

class RequestData
{
    private array $session;
    private array $uriParams;

    public function __construct(array $session, array $uriParams)
    {
        $this->session = $session;
        $this->uriParams = $uriParams;
    }

    public function getSession(): array
    {
        return $this->session;
    }

    public function getUriParams(): array
    {
        return $this->uriParams;
    }
}