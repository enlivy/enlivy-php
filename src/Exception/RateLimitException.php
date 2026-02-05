<?php

declare(strict_types=1);

namespace Enlivy\Exception;

class RateLimitException extends ApiException
{
    public function retryAfter(): ?int
    {
        foreach ($this->headers as $key => $value) {
            if (strtolower((string) $key) === 'retry-after') {
                return (int) (is_array($value) ? $value[0] : $value);
            }
        }

        return null;
    }
}
