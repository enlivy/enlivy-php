<?php

declare(strict_types=1);

require __DIR__ . '/../../../vendor/autoload.php';

use Enlivy\Enums\EventDelivery\TriggerEvent;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Webhook\WebhookEvent;
use Enlivy\Webhook\WebhookSignature;

$secret = getenv('ENLIVY_WEBHOOK_SECRET') ?: '';
$payload = (string) file_get_contents('php://input');
$signature = (string) ($_SERVER['HTTP_' . strtoupper(WebhookSignature::HEADER_NAME)] ?? '');

if ($secret === '') {
    http_response_code(500);
    exit('Set ENLIVY_WEBHOOK_SECRET to the event destination\'s signing secret.');
}

try {
    WebhookSignature::verify($payload, $signature, $secret);
} catch (InvalidArgumentException) {
    http_response_code(400);
    exit;
}

$event = WebhookEvent::fromPayload($payload);

match (TriggerEvent::tryFrom((string) $event->type)) {
    TriggerEvent::CHECKOUT_SESSION_COMPLETED => fulfil($event->data['client_reference_id'], (string) $event->data['id']),
    TriggerEvent::CHECKOUT_SESSION_EXPIRED => release($event->data['client_reference_id']),
    default => null,
};

http_response_code(204);

/**
 * Completion is reported once, but a delivery can still be retried: make fulfilment idempotent on the
 * session id. This is where a host marks its pending order paid and grants access.
 */
function fulfil(?string $orderId, string $sessionId): void
{
    error_log("checkout_session.completed {$sessionId} for order {$orderId}");
}

function release(?string $orderId): void
{
    error_log("checkout_session.expired for order {$orderId}");
}
