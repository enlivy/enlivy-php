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
    public const string VERSION = '2.7.0';

    public const string DEFAULT_API_BASE = 'https://api.enlivy.com';

    private static ?string $apiKey = null;

    private static ?string $clientId = null;

    private static ?string $clientSecret = null;

    private static ?string $accessToken = null;

    private static ?string $refreshToken = null;

    private static ?string $organizationId = null;

    private static string $apiBase = self::DEFAULT_API_BASE;

    private static bool $verifySslCerts = true;

    private static ?string $caBundlePath = null;

    private static int $maxNetworkRetries = 2;

    private static bool $enableTelemetry = true;

    private static int $timeout = 30;

    private static ?string $portalToken = null;

    /**
     * Callback for token refresh events.
     *
     * @var callable|null
     */
    private static $onTokenRefresh = null;

    public static function getApiKey(): ?string
    {
        return self::$apiKey;
    }

    public static function setApiKey(?string $apiKey): void
    {
        self::$apiKey = $apiKey;
    }

    public static function getClientId(): ?string
    {
        return self::$clientId;
    }

    public static function setClientId(?string $clientId): void
    {
        self::$clientId = $clientId;
    }

    public static function getClientSecret(): ?string
    {
        return self::$clientSecret;
    }

    public static function setClientSecret(?string $clientSecret): void
    {
        self::$clientSecret = $clientSecret;
    }

    public static function getAccessToken(): ?string
    {
        return self::$accessToken;
    }

    public static function setAccessToken(?string $accessToken): void
    {
        self::$accessToken = $accessToken;
    }

    public static function getRefreshToken(): ?string
    {
        return self::$refreshToken;
    }

    public static function setRefreshToken(?string $refreshToken): void
    {
        self::$refreshToken = $refreshToken;
    }

    public static function getOrganizationId(): ?string
    {
        return self::$organizationId;
    }

    public static function setOrganizationId(?string $organizationId): void
    {
        self::$organizationId = $organizationId;
    }

    public static function getPortalToken(): ?string
    {
        return self::$portalToken;
    }

    public static function setPortalToken(?string $portalToken): void
    {
        self::$portalToken = $portalToken;
    }

    public static function getApiBase(): string
    {
        return self::$apiBase;
    }

    public static function setApiBase(string $apiBase): void
    {
        self::$apiBase = $apiBase;
    }

    public static function getVerifySslCerts(): bool
    {
        return self::$verifySslCerts;
    }

    public static function setVerifySslCerts(bool $verify): void
    {
        self::$verifySslCerts = $verify;
    }

    public static function getCaBundlePath(): ?string
    {
        return self::$caBundlePath;
    }

    public static function setCaBundlePath(?string $path): void
    {
        self::$caBundlePath = $path;
    }

    public static function getMaxNetworkRetries(): int
    {
        return self::$maxNetworkRetries;
    }

    public static function setMaxNetworkRetries(int $maxRetries): void
    {
        self::$maxNetworkRetries = $maxRetries;
    }

    /**
     * Whether the SDK sends the anonymous client-runtime telemetry header
     * (SDK version, PHP version, OS family).
     */
    public static function getEnableTelemetry(): bool
    {
        return self::$enableTelemetry;
    }

    public static function setEnableTelemetry(bool $enable): void
    {
        self::$enableTelemetry = $enable;
    }

    public static function getTimeout(): int
    {
        return self::$timeout;
    }

    public static function setTimeout(int $timeout): void
    {
        self::$timeout = $timeout;
    }

    /**
     * @return callable|null
     */
    public static function getOnTokenRefresh(): ?callable
    {
        return self::$onTokenRefresh;
    }

    /**
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
        self::$enableTelemetry = true;
        self::$timeout = 30;
        self::$onTokenRefresh = null;
    }

    /**
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
