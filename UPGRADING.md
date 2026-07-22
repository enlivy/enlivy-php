# Upgrading to 2.3.0

`2.3.0` is additive except for one **wire-contract** change: billing-package
fields are no longer accepted on `billingSchedules->create()`. The SDK's PHP
surface is unchanged — `create()` keeps its signature — so you only need to
change code if you were creating package-backed schedules through it.

## Billing-package creation moved to `fromBillingPackage()`

`create()` now composes explicit `phases`/`payments` only. Move package-backed
creation to the dedicated endpoint:

```php
// Before — a package on create() is now rejected (422)
$schedule = $client->billingSchedules->create([
    'organization_billing_package_id' => 'org_bp_xxx',
    'organization_billing_package_subscription_term_id' => 'org_bp_st_xxx',
    'selected_group_items' => [['id' => 'org_bp_grpi_xxx', 'quantity' => 1]],
    'organization_sender_user_id' => 'org_user_s',
    'organization_receiver_user_id' => 'org_user_r',
    'status' => 'active',
]);

// After — same fields, dedicated endpoint
$schedule = $client->billingSchedules->fromBillingPackage([
    'organization_billing_package_id' => 'org_bp_xxx',
    'organization_billing_package_subscription_term_id' => 'org_bp_st_xxx',
    'selected_group_items' => [['id' => 'org_bp_grpi_xxx', 'quantity' => 1]],
    'organization_sender_user_id' => 'org_user_s',
    'organization_receiver_user_id' => 'org_user_r',
    'status' => 'active',
    'start_at' => null, // omit or null = start now
]);
```

`fromBillingPackage()` also bills the first cycle inline when the schedule is
created `active` and due; read the outcome from
`$schedule->lastResponse()?->json['meta']` (`charge_result`, `invoice_id`). See
[docs/organization/billing-packages.md](docs/organization/billing-packages.md).
Raw `create()` with `phases`/`payments` is unchanged.

# Upgrading to 2.0.0

`2.0.0` adds the tax-compliance engine, invoice refunds, and proposal-to-billing
-schedule support. All of that is **additive**. The major bump is for a handful
of **wire-contract** removals and renames below.

The SDK's PHP surface is source-compatible — no class, method, or enum case was
removed, and resources remain dynamic (reading a field the API no longer returns
yields `null` rather than an error). You only need to change code if you *write*
or *read* one of the fields below.

## 1. Products: `price_is_tax_inclusive` removed

Products no longer carry a `price_is_tax_inclusive` boolean. Tax treatment now
derives from the product's assigned tax class / category.

```php
// Before
$client->products->create([
    'name_lang_map' => ['en' => 'Consulting'],
    'price_is_tax_inclusive' => true, // no longer accepted
    // ...
]);

// After — omit it; assign a tax class instead
$client->products->create([
    'name_lang_map' => ['en' => 'Consulting'],
    'organization_tax_class_id' => 'org_tax_class_xxx',
    // ...
]);
```

If you read `$product->price_is_tax_inclusive`, it is no longer returned.

## 2. Tax rates: `country_code` → `seller_country_code`, `is_shipping` removed

On the tax-rate resource, `country_code` was renamed to `seller_country_code`
(and is now system-managed), and the `is_shipping` flag was removed. Tax-rate
locations take `country_code` and `zip_code` (the `iso_3166` write field is
gone).

```php
// Before
$rate->country_code;      // read
$client->taxRates->create([
    // ...
    'is_shipping' => false,
    'locations' => [['country_code' => 'US', 'iso_3166' => 'US-CA']],
]);

// After
$rate->seller_country_code;
$client->taxRates->create([
    // ...
    'locations' => [['country_code' => 'US', 'zip_code' => null]],
]);
```

## 3. Proposals: subscription-term field renamed

When creating a proposal from a billing package (`fromBillingPackage()`) or
claiming one through the Client Portal, the subscription cadence field is now
`organization_billing_package_subscription_term_id` (previously
`subscription_term_id`).

```php
// Before
$client->proposals->fromBillingPackage([
    'organization_billing_package_id' => 'org_bp_xxx',
    'subscription_term_id' => 'org_bp_sub_term_xxx',
]);

// After
$client->proposals->fromBillingPackage([
    'organization_billing_package_id' => 'org_bp_xxx',
    'organization_billing_package_subscription_term_id' => 'org_bp_sub_term_xxx',
]);
```

---

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
