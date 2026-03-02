<?php

declare(strict_types=1);

namespace Enlivy\Auth;

final readonly class ClientPortalAuth implements AuthHandlerInterface
{
    public function __construct(
        private string $portalToken,
    ) {}

    public function getHeaders(): array
    {
        return [
            'x-enlivy-client-api-key' => $this->portalToken,
        ];
    }

    public function canRefresh(): bool
    {
        return false;
    }

    public function refreshAccessToken(): bool
    {
        return false;
    }
}
