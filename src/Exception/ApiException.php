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

    /**
     * The stable name of a refusal the caller can act on (`error.code`), such as `route_denied`,
     * `tenant_feature_required`, `storage_limit_exceeded` or `email_sending_not_connected`; null when
     * the answer carries none. A 409 with a code needs something else to happen first; see `isConflict()`.
     */
    public function getRefusalCode(): ?string
    {
        $code = $this->body['error']['code'] ?? null;

        return is_string($code) && $code !== '' ? $code : null;
    }

    /**
     * What a client needs to act on the refusal: everything under `error` except `code`, such as
     * `denied_by`, `required_abilities` and `required_scopes` on `route_denied`, or `can_connect` on
     * `email_sending_not_connected`.
     */
    public function getFacts(): array
    {
        $error = $this->body['error'] ?? null;

        if (!is_array($error)) {
            return [];
        }

        unset($error['code']);

        return $error;
    }

    /**
     * True for a 409 without a code: the record changed since it was read (a stale revision, a
     * concurrent write, work already in flight). Reread, then retry once. A 409 with a code is not a
     * conflict of this kind; retrying cannot change it.
     */
    public function isConflict(): bool
    {
        return $this->statusCode === 409 && $this->getRefusalCode() === null;
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
