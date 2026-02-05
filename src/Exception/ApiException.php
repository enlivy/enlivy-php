<?php

declare(strict_types=1);

namespace Enlivy\Exception;

class ApiException extends \Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly ?array $body = null,
        public readonly array $headers = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public static function factory(int $statusCode, ?array $body, array $headers = []): self
    {
        $message = $body['message'] ?? "API error: HTTP {$statusCode}";

        return match (true) {
            $statusCode === 401 => new AuthenticationException($message, $statusCode, $body, $headers),
            $statusCode === 403 => new ForbiddenException($message, $statusCode, $body, $headers),
            $statusCode === 404 => new NotFoundException($message, $statusCode, $body, $headers),
            $statusCode === 422 => new ValidationException($message, $statusCode, $body, $headers),
            $statusCode === 429 => new RateLimitException($message, $statusCode, $body, $headers),
            $statusCode >= 500 => new ServerException($message, $statusCode, $body, $headers),
            default => new self($message, $statusCode, $body, $headers),
        };
    }
}
