<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Auth\ApiKeyAuth;
use Enlivy\Auth\AuthHandlerInterface;
use Enlivy\Auth\OAuthAuth;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\HttpClient\CurlClient;
use Enlivy\HttpClient\HttpClientInterface;
use Enlivy\Service\CoreServiceFactory;

class BaseEnlivyClient implements EnlivyClientInterface
{
    private readonly string $apiBase;
    private readonly ?string $organizationId;
    private readonly ApiRequestor $requestor;
    private readonly CoreServiceFactory $serviceFactory;

    /**
     * Create a new Enlivy client.
     *
     * When config is empty or missing values, falls back to Enlivy::get*() global config.
     *
     * @param array{
     *     api_key?: string,
     *     access_token?: string,
     *     refresh_token?: string,
     *     client_id?: string,
     *     client_secret?: string,
     *     organization_id?: string,
     *     api_base?: string,
     *     http_client?: HttpClientInterface,
     *     max_retries?: int,
     *     timeout?: int,
     *     on_token_refresh?: callable,
     * } $config
     */
    public function __construct(array $config = [])
    {
        // Merge with global config, explicit config takes precedence
        $config = $this->mergeWithGlobalConfig($config);

        $this->apiBase = rtrim($config['api_base'] ?? Enlivy::DEFAULT_API_BASE, '/');
        $this->organizationId = $config['organization_id'] ?? null;

        $httpClient = $config['http_client'] ?? new CurlClient();
        $authHandler = $this->resolveAuthHandler($config, $httpClient);

        $this->requestor = new ApiRequestor(
            authHandler: $authHandler,
            httpClient: $httpClient,
            apiBase: $this->apiBase,
            maxRetries: $config['max_retries'] ?? Enlivy::getMaxNetworkRetries(),
            timeout: $config['timeout'] ?? Enlivy::getTimeout(),
        );

        $this->serviceFactory = new CoreServiceFactory($this);
    }

    public function __get(string $name): mixed
    {
        return $this->serviceFactory->getService($name);
    }

    public function getRequestor(): ApiRequestor
    {
        return $this->requestor;
    }

    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    public function getApiBase(): string
    {
        return $this->apiBase;
    }

    /**
     * Merge provided config with global Enlivy config.
     *
     * Explicit config values take precedence over global config.
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function mergeWithGlobalConfig(array $config): array
    {
        // Only fill in missing values from global config
        return [
            'api_key' => $config['api_key'] ?? Enlivy::getApiKey(),
            'access_token' => $config['access_token'] ?? Enlivy::getAccessToken(),
            'refresh_token' => $config['refresh_token'] ?? Enlivy::getRefreshToken(),
            'client_id' => $config['client_id'] ?? Enlivy::getClientId(),
            'client_secret' => $config['client_secret'] ?? Enlivy::getClientSecret(),
            'organization_id' => $config['organization_id'] ?? Enlivy::getOrganizationId(),
            'api_base' => $config['api_base'] ?? Enlivy::getApiBase(),
            'max_retries' => $config['max_retries'] ?? Enlivy::getMaxNetworkRetries(),
            'timeout' => $config['timeout'] ?? Enlivy::getTimeout(),
            'on_token_refresh' => $config['on_token_refresh'] ?? Enlivy::getOnTokenRefresh(),
            // http_client is not in global config, pass through if provided
            'http_client' => $config['http_client'] ?? null,
        ];
    }

    private function resolveAuthHandler(array $config, HttpClientInterface $httpClient): AuthHandlerInterface
    {
        if (!empty($config['api_key'])) {
            return new ApiKeyAuth($config['api_key']);
        }

        if (!empty($config['access_token'])) {
            return new OAuthAuth(
                accessToken: $config['access_token'],
                refreshToken: $config['refresh_token'] ?? null,
                clientId: $config['client_id'] ?? null,
                clientSecret: $config['client_secret'] ?? null,
                tokenEndpoint: $this->apiBase . '/oauth/token',
                httpClient: $httpClient,
                onTokenRefresh: $config['on_token_refresh'] ?? null,
            );
        }

        throw new InvalidArgumentException(
            'You must provide either "api_key" or "access_token" in the client configuration or via Enlivy::setApiKey()/Enlivy::setAccessToken().',
        );
    }
}
