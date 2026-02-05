<?php

declare(strict_types=1);

namespace Enlivy\Auth;

use Enlivy\HttpClient\HttpClientInterface;

final class OAuthAuth implements AuthHandlerInterface
{
    private string $accessToken;
    private ?string $refreshToken;
    private ?\Closure $onTokenRefresh;

    public function __construct(
        string $accessToken,
        ?string $refreshToken = null,
        private readonly ?string $clientId = null,
        private readonly ?string $clientSecret = null,
        private readonly string $tokenEndpoint = '',
        private readonly HttpClientInterface $httpClient = new \Enlivy\HttpClient\CurlClient(),
        ?\Closure $onTokenRefresh = null,
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->onTokenRefresh = $onTokenRefresh;
    }

    public function getHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->accessToken}",
        ];
    }

    public function canRefresh(): bool
    {
        return $this->refreshToken !== null
            && $this->clientId !== null
            && $this->clientSecret !== null
            && $this->tokenEndpoint !== '';
    }

    public function refreshAccessToken(): bool
    {
        if (!$this->canRefresh()) {
            return false;
        }

        $response = $this->httpClient->request('POST', $this->tokenEndpoint, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ], [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ], 30);

        if ($response->statusCode !== 200 || $response->json === null) {
            return false;
        }

        $this->accessToken = $response->json['access_token'];

        if (isset($response->json['refresh_token'])) {
            $this->refreshToken = $response->json['refresh_token'];
        }

        if ($this->onTokenRefresh !== null) {
            ($this->onTokenRefresh)($this->accessToken, $this->refreshToken);
        }

        return true;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function onTokenRefresh(\Closure $callback): void
    {
        $this->onTokenRefresh = $callback;
    }
}
