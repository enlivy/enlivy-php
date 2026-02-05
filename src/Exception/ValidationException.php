<?php

declare(strict_types=1);

namespace Enlivy\Exception;

class ValidationException extends ApiException
{
    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        return $this->body['errors'] ?? [];
    }
}
