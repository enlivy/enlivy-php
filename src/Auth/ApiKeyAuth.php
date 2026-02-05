<?php

declare(strict_types=1);

namespace Enlivy\Auth;

final readonly class ApiKeyAuth implements AuthHandlerInterface
{
    public function __construct(
        private string $apiKey,
    ) {}

    public function getHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->apiKey}",
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
