# Checkout Sessions

Sell a billing package from your own server. You open a session for a customer with the order fixed;
the customer pays it in the browser with a token you hand them; when the money arrives, Enlivy creates
the billing schedule and issues its first invoice.

```
  your server (API key, this SDK)          the customer's browser               Enlivy
  checkoutSessions->create() ──────────────────────────────────────────────► order priced, held
  meta.client_token ─────────────────────► pays by card or transfer ─────────► settles
  checkout_session.completed, or retrieve() ◄──────────────────────────────── billing schedule + invoice
```

Nothing fiscal exists until the customer pays. A card is invoiced from the settled charge; a transfer
is asked for with a proforma, and the invoice follows once that is paid. A session abandoned at any
point expires and leaves nothing to cancel.

## Opening a session

```php
<?php

use Enlivy\EnlivyClient;
use Enlivy\Util\RequestOptions;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$session = $client->checkoutSessions->create([
    'organization_billing_package_id' => 'org_bp_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_billing_package_subscription_term_id' => 'org_bp_st_xxx',
    'selected_group_items' => [
        ['id' => 'org_bp_grpi_xxx', 'quantity' => 2],
    ],
    'client_reference_id' => 'order-1042',
    'metadata' => ['plan' => 'pro'],
], new RequestOptions(idempotencyKey: 'order-1042'));

$clientToken = $session->lastResponse()?->json['meta']['client_token'];

// Keep $session->id with your pending order. Give $clientToken to the browser, never the API key.
```

The token is answered once, on this response, and is not stored readable anywhere. It works for this
one session only: it can read the order, edit the customer's billing details and pay.

The browser sends it as the `X-Enlivy-Checkout-Session-Token` header, or your page renders the
checkout embed with it. This SDK does not call that lane: it belongs to the browser, not your server.
It does render the embed:

```php
<?php

use Enlivy\Embed\CheckoutEmbed;

$organization = $client->organizations->retrieve('org_xxx');

echo CheckoutEmbed::fromSession($session, $organization->customer_portal_base_url, [
    'theme' => 'light',   // or 'dark', 'auto'
    'locale' => 'ro',     // omitted: the visitor's browser
])->html($cspNonce ?? null);
```

`fromSession()` reads the token off the response that opened the session; a retrieved session has
none. Keep the token with your pending order and build the embed later with
`new CheckoutEmbed($portalBaseUrl, $clientToken, $billingPackageId)`. The page learns the payment was
taken from the `enlivy-portal:confirm` event on `document`; grant access only once a retrieve or
`checkout_session.completed` says `complete`. A runnable site doing all of this is in
[`examples/checkout`](../../examples/checkout/README.md).

| Field | Notes |
|-------|-------|
| `organization_billing_package_id` | Required |
| `organization_receiver_user_id` | Required; the customer, an organization user |
| `organization_sender_user_id` | Optional; defaults to the organization's owner |
| `organization_billing_package_subscription_term_id` | Subscription packages: the cadence variant; omitted, the package default |
| `selected_group_items` | Subscription packages: `[['id' => …, 'quantity' => …]]` group items |
| `organization_billing_package_payment_plan_id` | One-time packages: the payment plan sold |
| `line_quantities` | One-time packages: `[['id' => …, 'quantity' => …]]` payment-plan phase line items |
| `currency` | Optional; one the package is sold in |
| `start_at` | Optional; now or later. A later start takes no payment today |
| `expires_in_minutes` | Optional; 30 to 1440, default 1440 |
| `client_reference_id` | Optional, up to 255; your own order id, and a list filter |
| `metadata` | Optional; up to 20 string values, yours to read back |
| `source_channel`, `source_medium`, `source_campaign`, `source_term`, `source_content`, `source_click_id` | Optional; the campaign the buyer arrived from |

How quantities and group items compose is covered in
[Billing Packages](billing-packages.md#quantity-price-tiers).

### Retrying safely

Pass an `idempotencyKey` and a retry cannot open a second session:

| Retry with the same key | Answer |
|---|---|
| Same body, session still open | The same session, with a new `client_token` that replaces the last |
| Same body, session closed | The same session, `client_token` null |
| A different body | `422` |

With a key, the SDK also retries the create on its own after a dropped connection, a `429` or a
`5xx`. Keys are up to 255 characters.

### What a session refuses

`create()` answers `422` when the package is inactive or expired, does not end in a sale, comes with
contract templates (coming soon for checkouts), offers no payment method a checkout can take, or is
not sold in the `currency` asked for. Creating, correcting and expiring need the
`billing_schedules` feature pack and answer `402` without it; listing and reading do not.

### Showing a price first

To show what a session would charge before opening one, ask for a preview. It is priced exactly as
opening a session is, and nothing is saved. The buyer is a customer, or a country and entity type
for a visitor who is not one yet. Like opening a session, the preview needs the `billing_schedules`
feature pack and answers `402` without it.

```php
<?php

$preview = $client->misc->calculateBillingPackagePrice([
    'organization_billing_package_id' => 'org_bp_xxx',
    'country_code' => 'RO',
    'is_business_entity' => false,
    // or: 'organization_receiver_user_id' => 'org_user_xxx',
]);

$preview->mode;     // 'payment' or 'setup'
$preview->total;    // due today, a decimal string
$preview->payments; // a one-time plan's payments; `occurrences` is null when one repeats with no end
$preview->renewal;  // a subscription's renewal, else null
```

## Reading a session

```php
<?php

use Enlivy\Enums\CheckoutSession\PaymentStatuses;
use Enlivy\Enums\CheckoutSession\Statuses;

$session = $client->checkoutSessions->retrieve('org_chk_sess_xxx', [
    'include' => ['billing_schedule', 'invoice'],
]);

if (Statuses::tryFrom($session->status) === Statuses::COMPLETE) {
    grantAccess($session->client_reference_id, $session->organization_billing_schedule_id);
}
```

| Field | Values |
|-------|--------|
| `status` | `open`, then `complete` or `expired` (`Enums\CheckoutSession\Statuses`) |
| `payment_status` | `unpaid`, then `paid`, or `no_payment_required` when nothing was due (`PaymentStatuses`) |
| `mode` | `payment` when something is due today, `setup` when the card is only saved: a trial, a later start, a free first period (`Modes`) |
| `payment_method_kind` | `card` or `bank_transfer` once the customer chooses, else null (`Enums\Proposal\PaymentMethodKind`) |
| `priced_lines`, `priced_sub_total`, `priced_tax_total`, `priced_total` | What the customer is asked to pay today. The totals are decimal strings |
| `priced_terms` | `exchange_rates`, `conversion_fee` and the `taxes` behind those totals; for a one-time plan, `plan_payments`: each payment with `due_at`, `frequency`, `occurrences` and its totals. `occurrences` is null for a part that repeats with no end |
| `renewal` | Subscriptions: `frequency`, `total`, `currency`, `reconverts_each_renewal`, `first_charge_at`. Null for a one-time plan |
| `priced_at`, `price_held_until` | The price is held for an hour from the start of the checkout, then quoted afresh at the next payment step |
| `organization_proforma_invoice_id` | The proforma a transfer is paid against |
| `organization_billing_schedule_id`, `organization_invoice_id` | Set when the session completes. A `setup` session has no invoice |
| `expires_at`, `completed_at`, `expired_at` | A transfer keeps its session open until the proforma falls due, plus a week |

To be told rather than ask, subscribe an [event destination](event-destinations.md) to
`checkout_session.completed` and `checkout_session.expired` (`Enums\EventDelivery\TriggerEvent`).
Completion is reported once.

## Listing sessions

```php
<?php

$sessions = $client->checkoutSessions->list([
    'status' => 'open',
    'client_reference_id' => 'order-1042',
    'include' => 'receiver_user',
    'limit' => 25,
]);
```

| Filter | Notes |
|--------|-------|
| `status` | One of `open`, `complete`, `expired` |
| `client_reference_id` | Exact match |
| `organization_receiver_user_id` | One customer's sessions |
| `ids` | A list of session ids |

Sessions come newest first; `q` and `order_by` are not applied here. An unsold session is deleted a
week after it expires, so `retrieve()` answers `404` from then on. Completed ones are kept.

## Correcting a session

```php
<?php

$session = $client->checkoutSessions->update('org_chk_sess_xxx', [
    'client_reference_id' => 'order-1042-b',
    'source_campaign' => 'autumn-launch',
    'metadata' => ['plan' => 'pro', 'seats' => '5'],
    'expires_at' => '2026-10-20T12:00:00Z',
]);
```

| Field | Notes |
|-------|-------|
| `client_reference_id`, `source_*` | Any status; send null to clear |
| `metadata` | Any status; replaces the whole map, so send every key you keep |
| `expires_at` | Open sessions only; later than the current expiry and at most 30 days from now, else `422` |

The order, the customer, the package, the currency and the price never change, and any other field
you send is ignored. For a different purchase, expire the session and open a new one.

## Expiring a session

```php
<?php

$session = $client->checkoutSessions->expire('org_chk_sess_xxx');
```

Closes an open session so it can no longer be paid, and cancels its unpaid proforma. Afterwards its
token answers only the session's status and dates. A payment that went through completes the session
instead, and one still settling keeps it open, so read `status` on the answer. A session that is not
open answers `422`.

## Available Includes

| Include | Description |
|---------|-------------|
| `receiver_user` | The customer |
| `billing_package` | The package sold |
| `billing_schedule` | The schedule the session created |
| `invoice` | The first invoice |
| `proforma_invoice` | The proforma for a transfer |

## Related

- [Billing Packages](billing-packages.md): what a session can sell
- [Proposals](proposals.md): a sale to a named customer, by quote
- [Invoices](invoices.md): proformas and first invoices
- [Event Destinations](event-destinations.md): webhooks and their signatures
- [Enums](../enums.md): `CheckoutSession\Statuses`, `CheckoutSession\PaymentStatuses`, `CheckoutSession\Modes`
