# Upgrading to 2.5.0

`2.5.0` is a **minor** release. Almost all of it is additive. Three things are
worth a look before you upgrade: one renamed request parameter, one service whose
return types changed because they were wrong, and a set of validation rules that
tightened server-side.

## `misc->determineTaxRateId()` renamed `state` to `iso_3166`

The parameter takes an ISO 3166-2 subdivision code, which is what the rest of the
address surface already called it.

```php
// Before
$rate = $client->misc->determineTaxRateId([
    'country_code' => 'US',
    'state' => 'CA',
    'zip_code' => '94103',
]);

// After
$rate = $client->misc->determineTaxRateId([
    'country_code' => 'US',
    'iso_3166' => 'US-CA',
    'zip_code' => '94103',
]);
```

Parameters are pass-through arrays, so the SDK signature is unchanged — only the
key you send. This is a wire break in a minor release, deliberately: the endpoint
is a pass-through helper with no typed surface and no prior documentation, so a
major bump would have cost every consumer an upgrade for a problem none of them
had. If you cannot change the key yet, pin `2.4.x`.

## `userRoleAbilities` return types changed

All three methods were typed against a record resource the endpoints never
return, so **none of them worked**: `sync()` and `delete()` raised a `TypeError`
on every call, and `list()` handed back a `Collection` whose `getData()` was
always empty. They now return `EnlivyObject` holding the real payload.

```php
// Now works. Walk the list with toArray() or array access.
$abilities = $client->userRoleAbilities->list('org_role_xxx');

foreach ($abilities->toArray() as $row) {
    echo $row['ability'] . "\n";
}

$client->userRoleAbilities->sync('org_role_xxx', ['abilities' => ['invoices.manage']]);
$client->userRoleAbilities->delete('org_role_xxx', ['abilities' => ['invoices.manage']]);
```

Nothing that worked before changes, because nothing worked before. If you wrote
around the breakage — catching the `TypeError`, or reading the collection's
numeric keys — remove that workaround.

One behavioural note: a role with the new `has_full_backoffice_access` reports the
**whole** ability list rather than an empty one, as stand-in entries whose `id` is
`null`. Remove abilities by name, not by id.

## New 422s on payloads that used to slip past

No SDK change is needed for these, but a payload that the API quietly tolerated
may now be rejected:

- **Reorder endpoints** (tasks, task statuses, prospect statuses) reject ids
  belonging to another organization instead of ignoring them.
- **`is_stuck_threshold_days`** on prospect statuses must be an integer.
- **`convert_to_currency`** on billing-schedule analytics is now actually
  validated — the rule behind it never applied before.
- **Bank-transaction imports** accept only statement uploads and Stripe charge
  pulls. If you were sending a product, user or prospect import type to that
  lane, move to `$client->products`, `$client->organizationUsers` or
  `$client->prospects`, which now have their own import endpoints.

## Filters that used to be rejected now go through

`limit` is accepted on every list endpoint, and thirteen services regained
filters the API had always accepted (`title`/`description`, `name`/`description`,
`is_connected`, `reported_by_organization_user_id` — see the CHANGELOG for the
table). This only *loosens* client-side validation, so no working call changes.
If you built a workaround — dropping the key before calling, or catching
`InvalidArgumentException` — you can remove it.

## Optional: clearable address fields

`address_county`, `address_state` and `timezone` now accept `null` on
organizations and organization users, as does `address_city` on organization
users. Nothing to change unless you were working around the inability to clear
them.

# Upgrading to 2.4.0

`2.4.0` is a **minor** release. It does remove two methods, three enum cases and
a number of resource properties, and changes three signatures — but every one of
those targeted something the API does not serve: a path that returns 404, a value
the API never emits, or a field it never returns. **No code that works today can
break**, which is why this is not a major bump. Read on if you call any of them.

## Invoice log endpoints moved under `invoices/`

`invoice-charge-logs` is now `invoices/charge-logs`, and
`invoice-notification-logs` is now `invoices/notification-logs`.

**If you use the SDK, there is nothing to do.** `$client->invoiceChargeLogs` and
`$client->invoiceNotificationLogs` keep their names, methods and signatures — only
the paths they emit changed:

```php
// Unchanged - the SDK targets the new paths automatically
$logs = $client->invoiceChargeLogs->list(['organization_invoice_id' => 'org_inv_xxx']);
$sent = $client->invoiceNotificationLogs->list(['organization_invoice_id' => 'org_inv_xxx']);
```

This is a wire break shipped in a minor release, deliberately. Both are secondary
read-only endpoints with no traffic at the time of the move, so a major bump would
have cost every consumer an upgrade to fix a problem none of them had. If you call
the old paths directly with your own HTTP client, update them or pin `2.3.x`.

## OAuth consent endpoints require a first-party key

`/oauth/authorize/*` and `/oauth/authorizations/*` no longer accept an OAuth
access token. An access token approving its own consent could widen its own grant,
so these now require an API key:

```php
// Manage grants with an API-key client, not an OAuth-token one
$client = new \Enlivy\EnlivyClient(['api_key' => '1|token']);

$client->oauthAuthorizations->list();
$client->oauthAuthorizations->update('oauth_cua_xxx', ['scopes' => ['accounting:read']]);
```

## Calendar dates now serialize as `Y-m-d`

Fields backed by a calendar-day column previously came back as a midnight-UTC
timestamp, which rendered as the previous day in any negative-offset timezone.
They now return the plain date the docs and `/discovery` already declared:

```php
// Before: "2026-01-01T00:00:00.000000Z"
// After:  "2026-01-01"
echo $registration->effective_from;
```

Affected: `TaxRegistration.cash_accounting_from` / `cash_accounting_to` /
`effective_from` / `effective_to`, `TaxEvent.tax_point_date` / `document_date`,
`TaxFilingPeriod.period_start` / `period_end` / `filing_due_date` /
`payment_due_date`, `TaxFilingPeriodPayment.payment_date`, `Report.report_date`,
`OrganizationUser.birthdate`, and payment-method `expires_at`. Parsing with
`strtotime()` or `DateTimeImmutable` keeps working; string comparison against a
full timestamp does not.

## Methods that could not work are now correct

Three methods targeted paths the API does not serve, so no working code can be
calling them today — but the signatures changed:

```php
// Settings are set one key at a time
$client->settings->update('invoice_payment_reminder_is_enabled', ['value' => true]);
$client->userOrganizationSettings->update($userId, $orgId, 'locale', ['value' => 'ro']);
$client->userOrganizationSettings->delete($userId, $orgId, 'locale');
```

`billingSchedules->addTag()` / `removeTag()` are **removed** — billing schedules
have no tagging endpoints. The `tag_ids` include and filter are unaffected.

## Enum: `Contract\StateActionRequiredTypes`

Three cases were mirrored from a block that is commented out upstream and were
never emitted by the API. `PARTIES_SIGNATURES_REQUIRED` remains:

```php
// Removed - these were never real API values
StateActionRequiredTypes::RECEIVER_RECEIPT_CONFIRMATION_REQUIRED;
StateActionRequiredTypes::SENDER_SIGNATURE_REQUIRED;
StateActionRequiredTypes::SENDER_RECEIPT_CONFIRMATION_REQUIRED;
```

## Resource properties re-derived from the API

Every resource's `@property` list was regenerated from the response the API
actually returns. These are docblock annotations, so nothing changes at runtime —
but if your IDE or PHPStan previously accepted `$invoice->status_color` or
`$receipt->deleted_by_user_id`, those were always null and are now gone. Newly
declared properties (e.g. `$invoice->will_number_be_auto_assigned`,
`$bankAccount->sync_provider`) were already being returned.

## Stricter validation on write

`first_name` and `last_name` (registration, users, prospects, contract parties)
now reject non-name input, and registration passwords are length-bounded. Payloads
that previously slipped through may return 422.

---

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
