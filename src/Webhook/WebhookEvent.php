<?php

declare(strict_types=1);

namespace Enlivy\Webhook;

use Enlivy\EnlivyObject;

final class WebhookEvent
{
    public readonly string $id;
    public readonly string $type;
    public readonly string $createdAt;
    public readonly EnlivyObject $data;
    public readonly ?string $organizationId;

    public function __construct(array $payload)
    {
        $this->id = $payload['id'] ?? '';
        $this->type = $payload['type'] ?? '';
        $this->createdAt = $payload['created_at'] ?? '';
        $this->organizationId = $payload['organization_id'] ?? null;
        $this->data = EnlivyObject::constructFrom($payload['data'] ?? []);
    }

    /**
     * Parse a webhook payload from a raw JSON string.
     */
    public static function fromPayload(string $jsonPayload): self
    {
        $data = json_decode($jsonPayload, true, 512, JSON_THROW_ON_ERROR);

        return new self($data);
    }

    /**
     * Construct a verified webhook event.
     *
     * @throws \Enlivy\Exception\InvalidArgumentException If signature verification fails
     */
    public static function constructFrom(
        string $payload,
        string $signature,
        string $secret,
        int $tolerance = 300,
    ): self {
        WebhookSignature::verify($payload, $signature, $secret, $tolerance);

        return self::fromPayload($payload);
    }
}
