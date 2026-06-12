# Event Destinations

Receive real-time events when things happen in Enlivy. An **event destination**
is somewhere events are delivered to — either an HTTPS **webhook** endpoint or a
**Slack** channel. Each destination has one or more **subscriptions** (the events
it cares about), and every delivery attempt is recorded so you can inspect it.

> Migrating from the `0.x` `webhooks` API? See [UPGRADING.md](../../UPGRADING.md).

## Key concepts

- **Destination** — `type` (`webhook` or `slack`), plus where to deliver
  (`destination_url` for webhooks, `config.channel` for Slack).
- **Subscription** — an `event` to listen for, with optional `includes` (related
  data to embed in the payload).
- **Delivery** — a single attempt to deliver an event to a destination, with its
  request/response and `status`.

```php
'events' => [
    ['event' => 'invoice.created', 'includes' => ['line_items', 'receiver_user']],
    ['event' => 'invoice.updated', 'includes' => null],
]
```

## Creating a destination

### Webhook

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$destination = $client->eventDestinations->create([
    'type' => 'webhook',
    'destination_url' => 'https://your-app.com/webhooks/enlivy',
    'is_active' => true,
    'events' => [
        ['event' => 'invoice.created', 'includes' => null],
        ['event' => 'invoice.updated', 'includes' => null],
        ['event' => 'contract.created', 'includes' => null],
        ['event' => 'prospect.created', 'includes' => null],
    ],
]);

echo "Destination created: {$destination->id}\n";
echo "Signing secret: {$destination->signing_secret}\n"; // Store this securely!
```

### Slack

```php
<?php

$destination = $client->eventDestinations->create([
    'type' => 'slack',
    'name' => 'Finance alerts',
    'config' => ['channel' => '#finance'],
    'is_active' => true,
    'events' => [
        ['event' => 'invoice.paid', 'includes' => null],
        ['event' => 'contract.all_parties_signed', 'includes' => null],
    ],
]);
```

### With includes

Embed related data in the delivered payload:

```php
<?php

$destination = $client->eventDestinations->create([
    'type' => 'webhook',
    'destination_url' => 'https://your-app.com/webhooks/enlivy',
    'is_active' => true,
    'events' => [
        ['event' => 'invoice.created', 'includes' => ['line_items', 'receiver_user', 'sender_user']],
        ['event' => 'contract.created', 'includes' => ['parties', 'chapters']],
    ],
]);
```

## Available events

Event names are also available as the `Enlivy\Enums\EventDelivery\TriggerEvent`
enum (`TriggerEvent::values()` returns the full list). Common ones:

| Event | Description |
|-------|-------------|
| `invoice.created` / `invoice.updated` / `invoice.deleted` / `invoice.restored` | Invoice lifecycle |
| `invoice.paid` | Invoice was paid |
| `receipt.created` / `receipt.updated` / `receipt.deleted` / `receipt.restored` | Receipt lifecycle |
| `contract.created` / `contract.updated` / `contract.deleted` / `contract.restored` | Contract lifecycle |
| `contract.all_parties_signed` | Every party signed a contract |
| `contract_signature.created` / `contract_signature.updated` | Signature activity |
| `prospect.created` / `prospect.updated` / `prospect.deleted` / `prospect.restored` | Prospect lifecycle |
| `prospect_activity.created` / `prospect_activity.updated` | Prospect activity |
| `proposal.created` / `proposal.sent` / `proposal.accepted` / `proposal.rejected` / `proposal.expired` | Proposal lifecycle |
| `billing_package.created` / `billing_package.updated` | Billing package changes |
| `billing_schedule.created` / `billing_schedule.updated` | Billing schedule changes |
| `billing_scheduled_payment.created` / `billing_scheduled_payment.updated` | Scheduled payments |
| `payslip.created` / `payslip.updated` | Payslip changes |
| `product.created` / `product.updated` | Product changes |
| `project.created` / `project.member.created` | Project & membership changes |
| `bank_account.created` / `bank_transaction.created` | Banking changes |
| `network_exchange.created` / `network_exchange.updated` | PEPPOL network exchange |
| `user.created` / `user.updated` | Organization user changes |

## Listing destinations

```php
<?php

$destinations = $client->eventDestinations->list([
    'include' => 'event_subscriptions',
]);

foreach ($destinations as $destination) {
    $status = $destination->is_active ? 'Active' : 'Inactive';
    echo "[{$destination->type}] {$destination->destination_url} - {$status}\n";
}
```

Available includes: `organization`, `deleted_by_user`, `event_subscriptions`,
`event_deliveries`.

## Retrieving, updating, deleting

```php
<?php

$destination = $client->eventDestinations->retrieve('org_evt_dst_xxx');

// Replace the subscription set
$client->eventDestinations->update('org_evt_dst_xxx', [
    'events' => [
        ['event' => 'invoice.created', 'includes' => ['line_items']],
        ['event' => 'invoice.paid', 'includes' => null],
    ],
]);

// Disable without deleting
$client->eventDestinations->update('org_evt_dst_xxx', ['is_active' => false]);

// Delete
$client->eventDestinations->delete('org_evt_dst_xxx');
```

## Subscriptions & delivery logs

```php
<?php

// All subscriptions across the organization's destinations
$subscriptions = $client->eventDestinations->subscriptions();

// Delivery attempts, filterable
$deliveries = $client->eventDestinations->deliveries([
    'organization_event_destination_id' => 'org_evt_dst_xxx',
    'status' => ['failed'],
    'created_at_from' => '2026-06-01',
]);

foreach ($deliveries as $delivery) {
    echo "{$delivery->event} → {$delivery->status} ({$delivery->response_status})\n";
}

// A single delivery
$delivery = $client->eventDestinations->retrieveDelivery('org_evt_dlv_xxx');
```

Delivery filters: `organization_event_destination_id`, `event`, `status`,
`created_at_from`/`created_at_to`, `updated_at_from`/`updated_at_to`.

## Verifying webhook signatures

When receiving webhook deliveries, verify the signature to ensure authenticity.
This is unchanged across the `0.x` → `1.0` upgrade.

```php
<?php
// webhook-handler.php

use Enlivy\Webhook\WebhookSignature;
use Enlivy\Webhook\WebhookEvent;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_SIGNATURE'] ?? '';
$secret = 'your_signing_secret'; // The destination's signing_secret

try {
    WebhookSignature::verify($payload, $signature, $secret);
    $event = WebhookEvent::fromPayload($payload);

    echo "Event type: {$event->type}\n";

    switch ($event->type) {
        case 'invoice.created':
            handleInvoiceCreated($event->data);
            break;
        case 'invoice.paid':
            handleInvoicePaid($event->data);
            break;
        case 'contract.all_parties_signed':
            handleContractSigned($event->data);
            break;
    }

    http_response_code(200);
    echo json_encode(['received' => true]);
} catch (\Enlivy\Exception\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid signature']);
}
```

You can also verify and parse in one step with
`WebhookEvent::constructFrom($payload, $signature, $secret)`.

## Delivered payload structure

```json
{
    "id": "evt_xxx",
    "type": "invoice.created",
    "created_at": "2026-06-12T10:30:00Z",
    "data": {
        "id": "org_inv_xxx",
        "total": 1190.00,
        "currency": "EUR",
        "status": "draft",
        "line_items": [],
        "receiver_user": {}
    },
    "organization_id": "org_xxx"
}
```

## Laravel integration

```php
<?php
// routes/api.php (your Laravel app)

use Illuminate\Http\Request;
use Enlivy\Webhook\WebhookSignature;
use Enlivy\Webhook\WebhookEvent;

Route::post('/webhooks/enlivy', function (Request $request) {
    $payload = $request->getContent();
    $signature = $request->header('Signature');

    try {
        WebhookSignature::verify($payload, $signature, config('enlivy.webhook_secret'));
        $event = WebhookEvent::fromPayload($payload);

        dispatch(new ProcessEnlivyWebhook($event));

        return response()->json(['received' => true]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid'], 400);
    }
});
```

## Field reference

### Destination

| Field | Type | Description |
|-------|------|-------------|
| `type` | string | `webhook` or `slack` (see `EventDelivery\DestinationType`) |
| `destination_url` | string | HTTPS URL — required for `webhook` destinations |
| `config` | array | Type-specific config, e.g. `['channel' => '#finance']` for Slack |
| `name` | string | Optional label |
| `is_active` | boolean | Whether deliveries are sent (default: `true`) |
| `events` | array | Subscriptions to create — `[{event, includes}]` |
| `signing_secret` | string | Returned on creation; used to verify webhook payloads |

### Subscription object

| Field | Type | Description |
|-------|------|-------------|
| `event` | string | Event type (required) |
| `includes` | array | Related data to embed (optional) |

## Best practices

1. **Always verify signatures** — never process unverified webhook payloads.
2. **Respond quickly** — return `200` fast and process asynchronously.
3. **Handle duplicates** — an event may be delivered more than once.
4. **Store the signing secret** — it is shown once, on creation.
5. **Inspect failures** — use `deliveries(['status' => ['failed']])` to debug.

## Related

- [Event Trails](event-trails.md) — read-only audit history of changes
- [Integrations](../integrations.md) — third-party integrations (Slack, Stripe)
