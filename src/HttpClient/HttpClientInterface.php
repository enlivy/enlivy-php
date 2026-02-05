<?php

declare(strict_types=1);

namespace Enlivy\HttpClient;

use Enlivy\ApiResponse;

interface HttpClientInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?array $params = null,
        int $timeout = 30,
    ): ApiResponse;

    /**
     * Raw request that returns binary content (for file downloads).
     *
     * @param array<string, string> $headers
     */
    public function requestRaw(
        string $method,
        string $url,
        array $headers = [],
        ?array $params = null,
        int $timeout = 30,
    ): string;
}
