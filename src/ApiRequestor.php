<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Auth\AuthHandlerInterface;
use Enlivy\Exception\ApiException;
use Enlivy\HttpClient\HttpClientInterface;
use Enlivy\Util\RequestOptions;

final class ApiRequestor
{
    private const string SDK_VERSION = '1.0.0';

    public function __construct(
        private readonly AuthHandlerInterface $authHandler,
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiBase,
        private readonly int $maxRetries = 2,
        private readonly int $timeout = 30,
    ) {}

    /**
     * Make an API request and return the parsed response.
     */
    public function request(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
    ): ApiResponse {
        $url = $this->apiBase . $path;
        $headers = $this->buildHeaders($opts);

        $response = $this->httpClient->request($method, $url, $headers, $params, $this->timeout);

        // Handle 401 with OAuth auto-refresh
        if ($response->statusCode === 401 && $this->authHandler->canRefresh()) {
            $refreshed = $this->authHandler->refreshAccessToken();

            if ($refreshed) {
                $headers = $this->buildHeaders($opts);
                $response = $this->httpClient->request($method, $url, $headers, $params, $this->timeout);
            }
        }

        if ($response->statusCode >= 400) {
            throw ApiException::factory($response->statusCode, $response->json, $response->headers);
        }

        return $response;
    }

    /**
     * Make an API request and return a Collection (paginated list).
     */
    public function requestCollection(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
    ): Collection {
        $response = $this->request($method, $path, $params, $opts);

        /** @var Collection */
        return Collection::constructFrom($response->json ?? []);
    }

    /**
     * Make a raw request for binary content (file downloads).
     */
    public function requestRaw(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
    ): string {
        $url = $this->apiBase . $path;
        $headers = $this->buildHeaders($opts);

        return $this->httpClient->requestRaw($method, $url, $headers, $params, $this->timeout);
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(?RequestOptions $opts): array
    {
        $headers = $this->authHandler->getHeaders();

        $headers['Accept'] = 'application/json';
        $headers['User-Agent'] = 'Enlivy/PhpSDK/' . self::SDK_VERSION;

        if ($opts?->idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $opts->idempotencyKey;
        }

        if ($opts?->locale !== null) {
            $headers['Accept-Language'] = $opts->locale;
        }

        return $headers;
    }
}
