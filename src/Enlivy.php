<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Global configuration singleton for Enlivy SDK.
 *
 * Provides static access to API configuration, similar to Stripe\Stripe::setApiKey().
 *
 * Usage:
 *   Enlivy\Enlivy::setApiKey('1|your_token');
 *   Enlivy\Enlivy::setOrganizationId('org_xxx');
 *   $client = new Enlivy\EnlivyClient();
 */
final class Enlivy
{
    /**
     * SDK version.
     */
    public const string VERSION = '1.0.0';

    /**
     * Default API base URL.
     */
    public const string DEFAULT_API_BASE = 'https://api.enlivy.com';

    /**
     * The API key to use for requests.
     */
    private static ?string $apiKey = null;

    /**
     * OAuth client ID.
     */
    private static ?string $clientId = null;

    /**
     * OAuth client secret.
     */
    private static ?string $clientSecret = null;

    /**
     * OAuth access token.
     */
    private static ?string $accessToken = null;

    /**
     * OAuth refresh token.
     */
    private static ?string $refreshToken = null;

    /**
     * Default organization ID for all requests.
     */
    private static ?string $organizationId = null;

    /**
     * Base URL for the Enlivy API.
     */
    private static string $apiBase = self::DEFAULT_API_BASE;

    /**
     * Whether to verify SSL certificates.
     */
    private static bool $verifySslCerts = true;

    /**
     * Path to CA bundle file.
     */
    private static ?string $caBundlePath = null;

    /**
     * Maximum number of request retries.
     */
    private static int $maxNetworkRetries = 2;

    /**
     * Request timeout in seconds.
     */
    private static int $timeout = 30;

    /**
     * Client portal session token.
     */
    private static ?string $portalToken = null;

    /**
     * Callback for token refresh events.
     *
     * @var callable|null
     */
    private static $onTokenRefresh = null;

    /**
     * Get the API key.
     */
    public static function getApiKey(): ?string
    {
        return self::$apiKey;
    }

    /**
     * Set the API key for API requests.
     */
    public static function setApiKey(?string $apiKey): void
    {
        self::$apiKey = $apiKey;
    }

    /**
     * Get the OAuth client ID.
     */
    public static function getClientId(): ?string
    {
        return self::$clientId;
    }

    /**
     * Set the OAuth client ID.
     */
    public static function setClientId(?string $clientId): void
    {
        self::$clientId = $clientId;
    }

    /**
     * Get the OAuth client secret.
     */
    public static function getClientSecret(): ?string
    {
        return self::$clientSecret;
    }

    /**
     * Set the OAuth client secret.
     */
    public static function setClientSecret(?string $clientSecret): void
    {
        self::$clientSecret = $clientSecret;
    }

    /**
     * Get the OAuth access token.
     */
    public static function getAccessToken(): ?string
    {
        return self::$accessToken;
    }

    /**
     * Set the OAuth access token.
     */
    public static function setAccessToken(?string $accessToken): void
    {
        self::$accessToken = $accessToken;
    }

    /**
     * Get the OAuth refresh token.
     */
    public static function getRefreshToken(): ?string
    {
        return self::$refreshToken;
    }

    /**
     * Set the OAuth refresh token.
     */
    public static function setRefreshToken(?string $refreshToken): void
    {
        self::$refreshToken = $refreshToken;
    }

    /**
     * Get the default organization ID.
     */
    public static function getOrganizationId(): ?string
    {
        return self::$organizationId;
    }

    /**
     * Set the default organization ID for all requests.
     */
    public static function setOrganizationId(?string $organizationId): void
    {
        self::$organizationId = $organizationId;
    }

    /**
     * Get the client portal session token.
     */
    public static function getPortalToken(): ?string
    {
        return self::$portalToken;
    }

    /**
     * Set the client portal session token.
     */
    public static function setPortalToken(?string $portalToken): void
    {
        self::$portalToken = $portalToken;
    }

    /**
     * Get the API base URL.
     */
    public static function getApiBase(): string
    {
        return self::$apiBase;
    }

    /**
     * Set the API base URL.
     */
    public static function setApiBase(string $apiBase): void
    {
        self::$apiBase = $apiBase;
    }

    /**
     * Get whether SSL certificates are verified.
     */
    public static function getVerifySslCerts(): bool
    {
        return self::$verifySslCerts;
    }

    /**
     * Set whether to verify SSL certificates.
     */
    public static function setVerifySslCerts(bool $verify): void
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * Get the path to the CA bundle file.
     */
    public static function getCaBundlePath(): ?string
    {
        return self::$caBundlePath;
    }

    /**
     * Set the path to the CA bundle file.
     */
    public static function setCaBundlePath(?string $path): void
    {
        self::$caBundlePath = $path;
    }

    /**
     * Get the maximum number of network retries.
     */
    public static function getMaxNetworkRetries(): int
    {
        return self::$maxNetworkRetries;
    }

    /**
     * Set the maximum number of network retries.
     */
    public static function setMaxNetworkRetries(int $maxRetries): void
    {
        self::$maxNetworkRetries = $maxRetries;
    }

    /**
     * Get the request timeout in seconds.
     */
    public static function getTimeout(): int
    {
        return self::$timeout;
    }

    /**
     * Set the request timeout in seconds.
     */
    public static function setTimeout(int $timeout): void
    {
        self::$timeout = $timeout;
    }

    /**
     * Get the token refresh callback.
     *
     * @return callable|null
     */
    public static function getOnTokenRefresh(): ?callable
    {
        return self::$onTokenRefresh;
    }

    /**
     * Set the token refresh callback.
     *
     * Called when OAuth tokens are refreshed.
     * Signature: fn(string $accessToken, string $refreshToken): void
     */
    public static function setOnTokenRefresh(?callable $callback): void
    {
        self::$onTokenRefresh = $callback;
    }

    /**
     * Configure multiple settings at once.
     *
     * @param array{
     *     api_key?: string|null,
     *     client_id?: string|null,
     *     client_secret?: string|null,
     *     access_token?: string|null,
     *     refresh_token?: string|null,
     *     portal_token?: string|null,
     *     organization_id?: string|null,
     *     api_base?: string,
     *     verify_ssl_certs?: bool,
     *     ca_bundle_path?: string|null,
     *     max_network_retries?: int,
     *     timeout?: int,
     *     on_token_refresh?: callable|null,
     * } $config
     */
    public static function configure(array $config): void
    {
        if (isset($config['api_key'])) {
            self::setApiKey($config['api_key']);
        }
        if (isset($config['client_id'])) {
            self::setClientId($config['client_id']);
        }
        if (isset($config['client_secret'])) {
            self::setClientSecret($config['client_secret']);
        }
        if (isset($config['access_token'])) {
            self::setAccessToken($config['access_token']);
        }
        if (isset($config['refresh_token'])) {
            self::setRefreshToken($config['refresh_token']);
        }
        if (isset($config['portal_token'])) {
            self::setPortalToken($config['portal_token']);
        }
        if (isset($config['organization_id'])) {
            self::setOrganizationId($config['organization_id']);
        }
        if (isset($config['api_base'])) {
            self::setApiBase($config['api_base']);
        }
        if (isset($config['verify_ssl_certs'])) {
            self::setVerifySslCerts($config['verify_ssl_certs']);
        }
        if (isset($config['ca_bundle_path'])) {
            self::setCaBundlePath($config['ca_bundle_path']);
        }
        if (isset($config['max_network_retries'])) {
            self::setMaxNetworkRetries($config['max_network_retries']);
        }
        if (isset($config['timeout'])) {
            self::setTimeout($config['timeout']);
        }
        if (array_key_exists('on_token_refresh', $config)) {
            self::setOnTokenRefresh($config['on_token_refresh']);
        }
    }

    /**
     * Reset all configuration to defaults.
     *
     * Useful for testing.
     */
    public static function reset(): void
    {
        self::$apiKey = null;
        self::$clientId = null;
        self::$clientSecret = null;
        self::$accessToken = null;
        self::$refreshToken = null;
        self::$portalToken = null;
        self::$organizationId = null;
        self::$apiBase = self::DEFAULT_API_BASE;
        self::$verifySslCerts = true;
        self::$caBundlePath = null;
        self::$maxNetworkRetries = 2;
        self::$timeout = 30;
        self::$onTokenRefresh = null;
    }

    /**
     * Get all current configuration as an array.
     *
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        return [
            'api_key' => self::$apiKey,
            'client_id' => self::$clientId,
            'client_secret' => self::$clientSecret,
            'access_token' => self::$accessToken,
            'refresh_token' => self::$refreshToken,
            'portal_token' => self::$portalToken,
            'organization_id' => self::$organizationId,
            'api_base' => self::$apiBase,
            'verify_ssl_certs' => self::$verifySslCerts,
            'ca_bundle_path' => self::$caBundlePath,
            'max_network_retries' => self::$maxNetworkRetries,
            'timeout' => self::$timeout,
        ];
    }
}
