<?php

declare(strict_types=1);

namespace Enlivy\Webhook;

use Enlivy\Exception\InvalidArgumentException;

final class WebhookSignature
{
    /**
     * Verify a webhook signature.
     *
     * @param string $payload The raw request body
     * @param string $signature The signature from the webhook header
     * @param string $secret The webhook signing secret
     * @param int $tolerance Maximum age of the webhook in seconds (default 300 = 5 minutes)
     *
     * @throws InvalidArgumentException If the signature is invalid
     */
    public static function verify(
        string $payload,
        string $signature,
        string $secret,
        int $tolerance = 300,
    ): bool {
        $elements = explode(',', $signature);

        $timestamp = null;
        $signatures = [];

        foreach ($elements as $element) {
            [$prefix, $value] = explode('=', $element, 2);

            if ($prefix === 't') {
                $timestamp = (int) $value;
            } elseif ($prefix === 'v1') {
                $signatures[] = $value;
            }
        }

        if ($timestamp === null || $signatures === []) {
            throw new InvalidArgumentException('Invalid webhook signature format.');
        }

        if ($tolerance > 0 && abs(time() - $timestamp) > $tolerance) {
            throw new InvalidArgumentException('Webhook timestamp is outside the tolerance zone.');
        }

        $expectedSignature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($expectedSignature, $sig)) {
                return true;
            }
        }

        throw new InvalidArgumentException('Webhook signature verification failed.');
    }
}
