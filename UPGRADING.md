# Upgrading to 1.0.0

`1.0.0` is the first stable release. It contains three breaking changes from the
`0.x` series. Everything else is additive — if you don't use the APIs below, no
code changes are required.

## 1. Webhooks → Event Destinations

The webhook **management** API was replaced by a unified event-delivery system.
A webhook is now one *type* of "event destination" (the other is Slack). The
endpoints moved from `/webhooks` to `/event-destinations`, and the resource
shape changed (`url` → `destination_url`, plus `type`, `name` and `config`).

```php
// Before (0.x)
$hooks = $client->webhooks->list();
$hook  = $client->webhooks->create([
    'url'      => 'https://example.com/hooks',
    'is_active' => true,
]);
$events        = $client->webhooks->events();
$notifications = $client->webhooks->notifications();

// After (1.0)
$destinations = $client->eventDestinations->list();
$destination  = $client->eventDestinations->create([
    'type'            => 'webhook',
    'destination_url' => 'https://example.com/hooks',
    'is_active'       => true,
]);
$subscriptions = $client->eventDestinations->subscriptions();
$deliveries    = $client->eventDestinations->deliveries();
$delivery      = $client->eventDestinations->retrieveDelivery('org_evt_dlv_xxx');
```

Method mapping:

| 0.x (`webhooks`)        | 1.0 (`eventDestinations`)        |
| ----------------------- | -------------------------------- |
| `list()`                | `list()`                         |
| `retrieve($id)`         | `retrieve($id)`                  |
| `create()`              | `create()` (now takes `type`)    |
| `update($id)`           | `update($id)`                    |
| `delete($id)`           | `delete($id)`                    |
| `events()`              | `subscriptions()`                |
| `notifications()`       | `deliveries()`                   |
| `retrieveNotification()`| `retrieveDelivery()`             |

**Signature verification is unchanged.** If you receive webhook deliveries,
`Enlivy\Webhook\WebhookSignature` and `Enlivy\Webhook\WebhookEvent` work exactly
as before (the `Signature` header, HMAC-SHA256). No changes needed on the
receiving side.

## 2. Tenant-billing trial

The three trial endpoints collapsed into a single change-set call.

```php
// Before (0.x)
$client->tenantBillingTrial->activate();
$client->tenantBillingTrial->addPack(['pack' => 'pro']);
$client->tenantBillingTrial->dropPack('pro');

// After (1.0)
$client->tenantBillingTrial->applyChangeSet([
    'changes' => [
        ['type' => 'add',  'pack' => 'pro'],
        ['type' => 'drop', 'pack' => 'legacy'],
    ],
]);
```

The `add`/`drop` change types are available as
`Enlivy\Enums\TenantBilling\TrialChangeSetTypes`.

## 3. `PaymentMethodKind` enum value

`Enlivy\Enums\Proposal\PaymentMethodKind::SAVED_CARD` (`'saved_card'`) is now
`CARD` (`'card'`), matching the value the API actually returns.

```php
// Before
PaymentMethodKind::SAVED_CARD->value; // 'saved_card'

// After
PaymentMethodKind::CARD->value;       // 'card'
```

If you compared a proposal's payment-method kind against `'saved_card'`, compare
against `'card'` (or `PaymentMethodKind::CARD`) instead.
