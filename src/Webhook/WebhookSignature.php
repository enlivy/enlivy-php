<?php

declare(strict_types=1);

namespace Enlivy\Webhook;

use Enlivy\Exception\InvalidArgumentException;

final class WebhookSignature
{
    /**
     * The header name the Enlivy API uses for webhook signatures.
     */
    public const string HEADER_NAME = 'Signature';

    /**
     * The Enlivy API signs webhooks using HMAC-SHA256 of the raw JSON payload
     * and sends the hex-encoded hash in the `Signature` header.
     *
     * @param string $payload The raw request body (JSON)
     * @param string $signature The signature from the `Signature` header
     * @param string $secret The webhook signing secret
     *
     * @throws InvalidArgumentException If the signature is invalid or missing
     */
    public static function verify(
        string $payload,
        string $signature,
        string $secret,
    ): bool {
        if ($signature === '') {
            throw new InvalidArgumentException('Webhook signature is empty.');
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw new InvalidArgumentException('Webhook signature verification failed.');
        }

        return true;
    }
}
