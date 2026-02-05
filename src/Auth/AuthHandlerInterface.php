<?php

declare(strict_types=1);

namespace Enlivy\Auth;

interface AuthHandlerInterface
{
    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;

    /**
     * Whether this handler supports token refresh (OAuth only).
     */
    public function canRefresh(): bool;

    /**
     * Attempt to refresh the access token. Returns true on success.
     */
    public function refreshAccessToken(): bool;
}
