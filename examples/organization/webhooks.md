# Webhooks

Receive real-time notifications when events occur in Enlivy. Configure webhook endpoints to receive HTTP POST requests for specific events.

## Key Concepts

### Event Structure

Each event subscription specifies:
- `event`: The event type to subscribe to
- `includes`: Optional related data to include in the payload

```php
'events' => [
    ['event' => 'invoice.created', 'includes' => ['line_items', 'receiver_user']],
    ['event' => 'invoice.updated', 'includes' => null],
]
```

## Creating a Webhook Endpoint

### Basic Webhook

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$webhook = $client->webhooks->create([
    'destination_url' => 'https://your-app.com/webhooks/enlivy',
    'is_active' => true,
    'events' => [
        ['event' => 'invoice.created', 'includes' => null],
        ['event' => 'invoice.updated', 'includes' => null],
        ['event' => 'contract.created', 'includes' => null],
        ['event' => 'prospect.created', 'includes' => null],
    ],
]);

echo "Webhook created: {$webhook->id}\n";
echo "Secret: {$webhook->secret}\n"; // Store this securely!
```

### Webhook with Includes

Include related data in webhook payloads:

```php
<?php

$webhook = $client->webhooks->create([
    'destination_url' => 'https://your-app.com/webhooks/enlivy',
    'is_active' => true,
    'events' => [
        [
            'event' => 'invoice.created',
            'includes' => ['line_items', 'receiver_user', 'sender_user'],
        ],
        [
            'event' => 'invoice.updated',
            'includes' => ['line_items'],
        ],
        [
            'event' => 'contract.created',
            'includes' => ['parties', 'chapters'],
        ],
        [
            'event' => 'prospect.updated',
            'includes' => ['status'],
        ],
    ],
]);
```

## Available Events

### Invoice Events

| Event | Description |
|-------|-------------|
| `invoice.created` | Invoice was created |
| `invoice.updated` | Invoice was updated |
| `invoice.deleted` | Invoice was deleted |
| `invoice.restored` | Invoice was restored |

### Receipt Events

| Event | Description |
|-------|-------------|
| `receipt.created` | Receipt was created |
| `receipt.updated` | Receipt was updated |
| `receipt.deleted` | Receipt was deleted |
| `receipt.restored` | Receipt was restored |

### Contract Events

| Event | Description |
|-------|-------------|
| `contract.created` | Contract was created |
| `contract.updated` | Contract was updated |
| `contract.deleted` | Contract was deleted |
| `contract.restored` | Contract was restored |

### Prospect Events

| Event | Description |
|-------|-------------|
| `prospect.created` | Prospect was created |
| `prospect.updated` | Prospect was updated |
| `prospect.deleted` | Prospect was deleted |
| `prospect.restored` | Prospect was restored |

### Prospect Activity Events

| Event | Description |
|-------|-------------|
| `prospect_activity.created` | Activity was logged |
| `prospect_activity.updated` | Activity was updated |
| `prospect_activity.deleted` | Activity was deleted |
| `prospect_activity.restored` | Activity was restored |

### User Events

| Event | Description |
|-------|-------------|
| `user.created` | Organization user was created |
| `user.updated` | Organization user was updated |
| `user.deleted` | Organization user was deleted |
| `user.restored` | Organization user was restored |

### Bank Account Events

| Event | Description |
|-------|-------------|
| `bank_account.created` | Bank account was created |
| `bank_account.updated` | Bank account was updated |
| `bank_account.deleted` | Bank account was deleted |
| `bank_account.restored` | Bank account was restored |

### Bank Transaction Events

| Event | Description |
|-------|-------------|
| `bank_transaction.created` | Transaction was created |
| `bank_transaction.updated` | Transaction was updated |
| `bank_transaction.deleted` | Transaction was deleted |
| `bank_transaction.restored` | Transaction was restored |

### Project Events

| Event | Description |
|-------|-------------|
| `project.created` | Project was created |
| `project.updated` | Project was updated |
| `project.deleted` | Project was deleted |
| `project.restored` | Project was restored |
| `project.member.created` | Member was added to project |
| `project.member.updated` | Project member was updated |
| `project.member.deleted` | Member was removed from project |

### Billing Schedule Events

| Event | Description |
|-------|-------------|
| `billing_schedule.created` | Billing schedule was created |
| `billing_schedule.updated` | Billing schedule was updated |
| `billing_schedule.deleted` | Billing schedule was deleted |
| `billing_schedule.restored` | Billing schedule was restored |
| `billing_scheduled_payment.created` | Scheduled payment was created |
| `billing_scheduled_payment.updated` | Scheduled payment was updated |

### Playbook Events

| Event | Description |
|-------|-------------|
| `playbook.created` | Playbook was created |
| `playbook.updated` | Playbook was updated |
| `playbook.deleted` | Playbook was deleted |
| `playbook.restored` | Playbook was restored |

### Network Exchange Events

| Event | Description |
|-------|-------------|
| `network_exchange.created` | PEPPOL network exchange was created |
| `network_exchange.updated` | PEPPOL network exchange was updated |

### Payslip Events

| Event | Description |
|-------|-------------|
| `payslip.created` | Payslip was created |
| `payslip.updated` | Payslip was updated |
| `payslip.deleted` | Payslip was deleted |
| `payslip.restored` | Payslip was restored |

## Listing Webhooks

```php
<?php

$webhooks = $client->webhooks->list();

foreach ($webhooks as $webhook) {
    $status = $webhook->is_active ? 'Active' : 'Inactive';
    echo "{$webhook->destination_url} - {$status}\n";

    echo "  Events:\n";
    foreach ($webhook->events as $event) {
        $includes = !empty($event['includes']) ? ' (includes: ' . implode(', ', $event['includes']) . ')' : '';
        echo "    - {$event['event']}{$includes}\n";
    }
}
```

## Retrieving a Webhook

```php
<?php

$webhook = $client->webhooks->retrieve('org_webhook_xxx');

echo "Webhook: {$webhook->id}\n";
echo "URL: {$webhook->destination_url}\n";
echo "Active: " . ($webhook->is_active ? 'Yes' : 'No') . "\n";
echo "Events: " . count($webhook->events) . "\n";
```

## Updating a Webhook

```php
<?php

$webhook = $client->webhooks->update('org_webhook_xxx', [
    'events' => [
        ['event' => 'invoice.created', 'includes' => ['line_items']],
        ['event' => 'invoice.updated', 'includes' => ['line_items']],
        ['event' => 'invoice.deleted', 'includes' => null],
    ],
]);

echo "Webhook updated\n";
```

## Disabling a Webhook

```php
<?php

$webhook = $client->webhooks->update('org_webhook_xxx', [
    'is_active' => false,
]);

echo "Webhook disabled\n";
```

## Deleting a Webhook

```php
<?php

$client->webhooks->delete('org_webhook_xxx');

echo "Webhook deleted\n";
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
        case 'invoice.created':
            $invoice = $event->data;
            handleInvoiceCreated($invoice);
            break;

        case 'invoice.updated':
            $invoice = $event->data;
            handleInvoiceUpdated($invoice);
            break;

        case 'contract.updated':
            $contract = $event->data;
            handleContractUpdated($contract);
            break;

        case 'prospect.updated':
            $prospect = $event->data;
            handleProspectUpdated($prospect);
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
    "type": "invoice.created",
    "created_at": "2026-02-05T10:30:00Z",
    "data": {
        "id": "org_inv_xxx",
        "invoice_number": "INV-2026-0001",
        "total": 1190.00,
        "currency": "EUR",
        "status": "draft",
        "line_items": [...],
        "receiver_user": {...}
    },
    "organization_id": "org_xxx"
}
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

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `destination_url` | string | HTTPS URL to receive webhook payloads |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `is_active` | boolean | Whether the webhook is active (default: true) |
| `events` | array | Array of event subscriptions |

### Event Object Fields

| Field | Type | Description |
|-------|------|-------------|
| `event` | string | Event type (required) |
| `includes` | array | Related data to include (optional) |

## Best Practices

1. **Always verify signatures** - Never process unverified webhooks
2. **Respond quickly** - Return 200 within 5 seconds, process async
3. **Handle duplicates** - Events may be sent multiple times
4. **Use HTTPS** - Always use secure endpoints
5. **Log events** - Keep records for debugging
6. **Store the secret** - Save the webhook secret securely on creation

## Complete Example: Webhook Setup

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // Create webhook for key business events
    $webhook = $client->webhooks->create([
        'destination_url' => 'https://your-app.com/webhooks/enlivy',
        'is_active' => true,
        'events' => [
            // Invoice events with full details
            ['event' => 'invoice.created', 'includes' => ['line_items', 'receiver_user']],
            ['event' => 'invoice.updated', 'includes' => ['line_items']],

            // Contract events
            ['event' => 'contract.created', 'includes' => ['parties']],
            ['event' => 'contract.updated', 'includes' => ['parties']],

            // Prospect pipeline events
            ['event' => 'prospect.created', 'includes' => ['status']],
            ['event' => 'prospect.updated', 'includes' => ['status']],

            // User events
            ['event' => 'user.created', 'includes' => null],
            ['event' => 'user.updated', 'includes' => null],
        ],
    ]);

    echo "Webhook created!\n";
    echo "ID: {$webhook->id}\n";
    echo "Secret: {$webhook->secret}\n";
    echo "\n";
    echo "IMPORTANT: Store the secret securely. It cannot be retrieved later.\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Integrations](integrations.md) - Third-party integrations
