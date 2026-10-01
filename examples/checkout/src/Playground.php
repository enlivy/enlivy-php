<?php

declare(strict_types=1);

namespace Example\Checkout;

use Enlivy\EnlivyClient;
use Enlivy\Enums\Organization\Environments;
use Enlivy\Organization;

/**
 * What this example keeps between requests. A real host keeps its API key in its server configuration
 * and the client token with its pending order, never in a visitor's session.
 */
final class Playground
{
    public static function connect(string $apiKey, string $apiBase): void
    {
        session_regenerate_id(true);
        $_SESSION = ['api_key' => $apiKey, 'api_base' => $apiBase, 'csrf' => bin2hex(random_bytes(16))];
    }

    public static function disconnect(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function isConnected(): bool
    {
        return isset($_SESSION['api_key']);
    }

    public static function client(?string $organizationId = null): EnlivyClient
    {
        return new EnlivyClient(array_filter([
            'api_key' => $_SESSION['api_key'] ?? '',
            'api_base' => $_SESSION['api_base'] ?? null,
            'organization_id' => $organizationId ?? self::organizationId(),
        ]));
    }

    public static function useOrganization(Organization $organization): void
    {
        $_SESSION['organization'] = [
            'id' => $organization->id,
            'name' => $organization->name,
            'environment' => $organization->environment,
            'portal' => $organization->customer_portal_base_url,
        ];
    }

    public static function organizationId(): ?string
    {
        return $_SESSION['organization']['id'] ?? null;
    }

    /**
     * @return array{id: string, name: string, environment: ?string, portal: ?string}|null
     */
    public static function organization(): ?array
    {
        return $_SESSION['organization'] ?? null;
    }

    public static function isSandbox(): bool
    {
        return (self::organization()['environment'] ?? null) === Environments::SANDBOX->value;
    }

    public static function rememberClientToken(string $sessionId, string $token): void
    {
        $_SESSION['client_tokens'][$sessionId] = $token;
    }

    public static function clientToken(string $sessionId): ?string
    {
        return $_SESSION['client_tokens'][$sessionId] ?? null;
    }

    public static function csrf(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    }

    public static function checkCsrf(): void
    {
        if (! hash_equals(self::csrf(), (string) ($_POST['csrf'] ?? ''))) {
            http_response_code(419);
            exit('The form expired. Go back and try again.');
        }
    }

    public static function flash(?string $message = null, string $tone = 'ok'): ?array
    {
        if ($message !== null) {
            $_SESSION['flash'] = ['message' => $message, 'tone' => $tone];

            return null;
        }
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
