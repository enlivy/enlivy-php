<?php

declare(strict_types=1);

namespace Enlivy;

final readonly class ApiResponse
{
    public function __construct(
        public int $statusCode,
        public array $headers,
        public string $body,
        public ?array $json = null,
    ) {}

    public static function fromRaw(int $statusCode, array $headers, string $body): self
    {
        $json = json_decode($body, true);

        return new self(
            statusCode: $statusCode,
            headers: $headers,
            body: $body,
            json: is_array($json) ? $json : null,
        );
    }

    public function getHeader(string $name): ?string
    {
        $lower = strtolower($name);

        foreach ($this->headers as $key => $value) {
            if (strtolower((string) $key) === $lower) {
                return is_array($value) ? $value[0] : (string) $value;
            }
        }

        return null;
    }
}
