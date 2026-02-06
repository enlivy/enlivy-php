<?php

declare(strict_types=1);

namespace Enlivy\HttpClient;

use Enlivy\ApiResponse;
use Enlivy\Exception\ApiConnectionException;

final class CurlClient implements HttpClientInterface
{
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?array $params = null,
        int $timeout = 30,
    ): ApiResponse {
        [$responseBody, $responseHeaders, $statusCode] = $this->executeRequest(
            $method,
            $url,
            $headers,
            $params,
            $timeout,
        );

        return ApiResponse::fromRaw($statusCode, $responseHeaders, $responseBody);
    }

    public function requestRaw(
        string $method,
        string $url,
        array $headers = [],
        ?array $params = null,
        int $timeout = 30,
    ): string {
        [$responseBody] = $this->executeRequest($method, $url, $headers, $params, $timeout);

        return $responseBody;
    }

    /**
     * @return array{string, array<string, string>, int}
     */
    private function executeRequest(
        string $method,
        string $url,
        array $headers,
        ?array $params,
        int $timeout,
    ): array {
        $ch = curl_init();

        $method = strtoupper($method);

        $curlHeaders = ['Accept: application/json'];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = "{$key}: {$value}";
        }

        if (in_array($method, ['POST', 'PUT', 'PATCH'], true) && $params !== null) {
            $body = json_encode($params, JSON_THROW_ON_ERROR);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            $curlHeaders[] = 'Content-Type: application/json';
        } elseif ($method === 'GET' && $params !== null && $params !== []) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HEADER => true,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);

            throw new ApiConnectionException(
                "Could not connect to Enlivy API: {$error} (errno {$errno})",
            );
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $rawHeaders = substr((string) $response, 0, $headerSize);
        $body = substr((string) $response, $headerSize);

        $parsedHeaders = $this->parseHeaders($rawHeaders);

        return [$body, $parsedHeaders, $statusCode];
    }

    /**
     * @return array<string, string>
     */
    private function parseHeaders(string $rawHeaders): array
    {
        $headers = [];

        foreach (explode("\r\n", $rawHeaders) as $line) {
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }

        return $headers;
    }
}
