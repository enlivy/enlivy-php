<?php

declare(strict_types=1);

namespace Enlivy\Util;

final readonly class RequestOptions
{
    public function __construct(
        public ?string $organizationId = null,
        public ?string $idempotencyKey = null,
        public ?string $locale = null,
    ) {}
}
