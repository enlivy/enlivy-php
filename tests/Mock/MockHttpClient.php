<?php

declare(strict_types=1);

namespace Enlivy\Tests\Mock;

use Enlivy\ApiResponse;
use Enlivy\HttpClient\HttpClientInterface;

/**
 * Mock HTTP client for testing.
 */
final class MockHttpClient implements HttpClientInterface
{
    /** @var list<array{method: string, url: string, headers: array, params: ?array}> */
    private array $requests = [];

    /** @var list<ApiResponse> */
    private array $responses = [];

    private int $responseIndex = 0;

    public function addResponse(int $statusCode, array $json, array $headers = []): self
    {
        $this->responses[] = new ApiResponse(
            statusCode: $statusCode,
            headers: $headers,
            body: json_encode($json, JSON_THROW_ON_ERROR),
            json: $json,
        );

        return $this;
    }

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?array $params = null,
        int $timeout = 30,
    ): ApiResponse {
        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'params' => $params,
        ];

        if (!isset($this->responses[$this->responseIndex])) {
            return new ApiResponse(
                statusCode: 200,
                headers: [],
                body: '{}',
                json: [],
            );
        }

        return $this->responses[$this->responseIndex++];
    }

    public function requestRaw(
        string $method,
        string $url,
        array $headers = [],
        ?array $params = null,
        int $timeout = 30,
    ): string {
        $response = $this->request($method, $url, $headers, $params, $timeout);

        return $response->body;
    }

    /**
     * @return list<array{method: string, url: string, headers: array, params: ?array}>
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    public function getLastRequest(): ?array
    {
        return $this->requests[count($this->requests) - 1] ?? null;
    }

    public function getRequestCount(): int
    {
        return count($this->requests);
    }

    public function reset(): void
    {
        $this->requests = [];
        $this->responses = [];
        $this->responseIndex = 0;
    }
}
