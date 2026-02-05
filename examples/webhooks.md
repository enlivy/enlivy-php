# Webhooks

Receive real-time notifications when events occur in Enlivy.

## Creating a Webhook Endpoint

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$webhook = $client->webhooks->create([
    'url' => 'https://your-app.com/webhooks/enlivy',
    'events' => [
        'invoice.created',
        'invoice.paid',
        'contract.signed',
        'prospect.status_changed',
    ],
    'is_active' => true,
]);

echo "Webhook created: {$webhook->id}\n";
echo "Secret: {$webhook->secret}\n"; // Store this securely!
```

## Available Events

### Invoice Events
- `invoice.created`
- `invoice.updated`
- `invoice.sent`
- `invoice.paid`
- `invoice.deleted`

### Contract Events
- `contract.created`
- `contract.updated`
- `contract.signed`
- `contract.deleted`

### Prospect Events
- `prospect.created`
- `prospect.updated`
- `prospect.status_changed`
- `prospect.deleted`

### User Events
- `organization_user.created`
- `organization_user.updated`
- `organization_user.deleted`

## Listing Webhooks

```php
<?php

$webhooks = $client->webhooks->list();

foreach ($webhooks as $webhook) {
    $status = $webhook->is_active ? 'Active' : 'Inactive';
    echo "{$webhook->url} - {$status}\n";
    echo "  Events: " . implode(', ', $webhook->events) . "\n";
}
```

## Verifying Webhook Signatures

When receiving webhooks, verify the signature to ensure authenticity.

```php
<?php
// webhook-handler.php

use Enlivy\Webhook\WebhookSignature;
use Enlivy\Webhook\WebhookEvent;

// Get the raw payload
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_ENLIVY_SIGNATURE'] ?? '';
$secret = 'your_webhook_secret'; // From webhook creation

try {
    // Verify signature
    WebhookSignature::verify($payload, $signature, $secret);

    // Parse the event
    $event = WebhookEvent::fromPayload($payload);

    echo "Event type: {$event->type}\n";
    echo "Event ID: {$event->id}\n";

    // Handle the event
    switch ($event->type) {
        case 'invoice.paid':
            $invoice = $event->data;
            handleInvoicePaid($invoice);
            break;

        case 'contract.signed':
            $contract = $event->data;
            handleContractSigned($contract);
            break;

        case 'prospect.status_changed':
            $prospect = $event->data;
            handleProspectStatusChanged($prospect);
            break;
    }

    http_response_code(200);
    echo json_encode(['received' => true]);

} catch (\Enlivy\Exception\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid signature']);
}
```

## Webhook Event Structure

```json
{
    "id": "evt_xxx",
    "type": "invoice.paid",
    "created_at": "2026-02-05T10:30:00Z",
    "data": {
        "id": "org_inv_xxx",
        "number": "INV-2026-0001",
        "total": 1190.00,
        "currency": "EUR",
        "status": "paid",
        "paid_at": "2026-02-05T10:29:55Z"
    },
    "organization_id": "org_xxx"
}
```

## Updating a Webhook

```php
<?php

$webhook = $client->webhooks->update('webhook_xxx', [
    'events' => [
        'invoice.created',
        'invoice.paid',
        'invoice.deleted',
    ],
]);
```

## Disabling a Webhook

```php
<?php

$webhook = $client->webhooks->update('webhook_xxx', [
    'is_active' => false,
]);
```

## Deleting a Webhook

```php
<?php

$client->webhooks->delete('webhook_xxx');
```

## Laravel Integration

```php
<?php
// routes/api.php

use Illuminate\Http\Request;
use Enlivy\Webhook\WebhookSignature;
use Enlivy\Webhook\WebhookEvent;

Route::post('/webhooks/enlivy', function (Request $request) {
    $payload = $request->getContent();
    $signature = $request->header('X-Enlivy-Signature');

    try {
        WebhookSignature::verify($payload, $signature, config('enlivy.webhook_secret'));
        $event = WebhookEvent::fromPayload($payload);

        // Dispatch to job for async processing
        dispatch(new ProcessEnlivyWebhook($event));

        return response()->json(['received' => true]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid'], 400);
    }
});
```

## Best Practices

1. **Always verify signatures** - Never process unverified webhooks
2. **Respond quickly** - Return 200 within 5 seconds, process async
3. **Handle duplicates** - Events may be sent multiple times
4. **Use HTTPS** - Always use secure endpoints
5. **Log events** - Keep records for debugging

## Related

- [Integrations](integrations.md) - Third-party integrations
