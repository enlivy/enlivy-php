<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Auth\AuthHandlerInterface;
use Enlivy\Exception\ApiConnectionException;
use Enlivy\Exception\ApiException;
use Enlivy\HttpClient\HttpClientInterface;
use Enlivy\Util\RequestOptions;

final class ApiRequestor
{
    private const array RETRYABLE_STATUS_CODES = [429, 500, 502, 503, 504];

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
        $attempt = 0;

        $timeout = $opts->timeout ?? $this->timeout;

        while (true) {
            try {
                $headers = $this->buildHeaders($opts);
                $response = $this->httpClient->request($method, $url, $headers, $params, $timeout);

                // Handle 401 with OAuth auto-refresh
                if ($response->statusCode === 401 && $this->authHandler->canRefresh()) {
                    $refreshed = $this->authHandler->refreshAccessToken();

                    if ($refreshed) {
                        $headers = $this->buildHeaders($opts);
                        $response = $this->httpClient->request($method, $url, $headers, $params, $timeout);
                    }
                }
            } catch (ApiConnectionException $e) {
                if ($this->shouldRetry($method, null, $opts, $attempt)) {
                    $this->sleepBeforeRetry($attempt, null);
                    $attempt++;
                    continue;
                }

                throw $e;
            }

            if ($response->statusCode >= 400) {
                if ($this->shouldRetry($method, $response, $opts, $attempt)) {
                    $this->sleepBeforeRetry($attempt, $response);
                    $attempt++;
                    continue;
                }

                throw ApiException::factory($response->statusCode, $response->json, $response->headers);
            }

            return $response;
        }
    }

    /**
     * Make an API request and return a Collection (paginated list).
     *
     * @template T of EnlivyObject
     * @param class-string<T>|null $resourceClass The class for items in the collection
     * @return Collection<T>
     */
    public function requestCollection(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
        ?string $resourceClass = null,
    ): Collection {
        $response = $this->request($method, $path, $params, $opts);

        $data = $response->json ?? [];

        // Create collection and hydrate items with the specified class
        /** @var Collection<T> $collection */
        $collection = new Collection();
        $collection->refreshFromWithClass($data, $resourceClass);
        $collection->setRequestContext($this, $method, $path, $params, $opts, $resourceClass);

        return $collection;
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
        $attempt = 0;
        $timeout = $opts->timeout ?? $this->timeout;

        while (true) {
            $headers = $this->buildHeaders($opts);

            try {
                return $this->httpClient->requestRaw($method, $url, $headers, $params, $timeout);
            } catch (ApiConnectionException $e) {
                if ($this->shouldRetry($method, null, $opts, $attempt)) {
                    $this->sleepBeforeRetry($attempt, null);
                    $attempt++;
                    continue;
                }

                throw $e;
            }
        }
    }

    /**
     * Retries are limited to requests that are safe to replay: GETs, or
     * writes the caller made idempotent via an Idempotency-Key.
     */
    private function shouldRetry(string $method, ?ApiResponse $response, ?RequestOptions $opts, int $attempt): bool
    {
        if ($attempt >= $this->maxRetries) {
            return false;
        }

        if (strtoupper($method) !== 'GET' && $opts?->idempotencyKey === null) {
            return false;
        }

        if ($response === null) {
            return true;
        }

        return in_array($response->statusCode, self::RETRYABLE_STATUS_CODES, true);
    }

    private function sleepBeforeRetry(int $attempt, ?ApiResponse $response): void
    {
        $delay = min(2.0, 0.5 * (2 ** $attempt));
        $delay *= 0.5 + (mt_rand(0, 1000) / 2000);

        $retryAfter = $response?->getHeader('Retry-After');
        if ($retryAfter !== null && is_numeric($retryAfter)) {
            $delay = max($delay, min(5.0, (float) $retryAfter));
        }

        usleep((int) ($delay * 1_000_000));
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(?RequestOptions $opts): array
    {
        $headers = $this->authHandler->getHeaders();

        $headers['Accept'] = 'application/json';
        $headers['User-Agent'] = 'Enlivy/PhpSDK/' . Enlivy::VERSION;

        if (Enlivy::getEnableTelemetry()) {
            $headers['X-Enlivy-Client-User-Agent'] = json_encode([
                'sdk' => 'enlivy-php',
                'sdk_version' => Enlivy::VERSION,
                'lang' => 'php',
                'lang_version' => PHP_VERSION,
                'os' => PHP_OS_FAMILY,
            ], JSON_THROW_ON_ERROR);
        }

        if ($opts?->idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $opts->idempotencyKey;
        }

        if ($opts?->locale !== null) {
            $headers['Accept-Language'] = $opts->locale;
        }

        foreach ($opts->headers ?? [] as $name => $value) {
            $headers[$name] = $value;
        }

        return $headers;
    }
}
