# Enums

The SDK ships string-backed PHP enums under `Enlivy\Enums\` that mirror the
fixed value sets the API accepts and returns. Use them for type-safe request
building and response validation instead of hard-coded strings.

> Human-readable labels, colors and translations are **not** part of these
> enums — they are locale-dependent and change at runtime. Fetch them from the
> frontend bootstrap endpoint via `$client->frontend`.

## Usage

```php
<?php

use Enlivy\Enums\Invoice\Statuses;
use Enlivy\Enums\Payment\PaymentMethodProvider;

// Build requests with typed values
$client->invoices->update('org_inv_xxx', [
    'status' => Statuses::PAID->value,
]);

// Validate untrusted input
if (! Statuses::isValid($incoming)) {
    throw new \InvalidArgumentException("Unknown invoice status: {$incoming}");
}

// Resolve a response value back to a case (null-safe)
$status = Statuses::tryFrom($invoice->status);

// Enumerate
PaymentMethodProvider::values();   // ['stripe', 'paypal']
PaymentMethodProvider::names();    // ['STRIPE', 'PAYPAL']
PaymentMethodProvider::cases();    // native PHP enum cases
```

## Helpers

Every enum uses the `Enlivy\Enums\Concern\EnumValues` trait, on top of native
PHP enum methods:

| Method | Returns |
|--------|---------|
| `values()` | `list<string>` of backing values |
| `names()` | `list<string>` of case names |
| `isValid(string $v)` | `bool` — is `$v` a valid backing value |
| `from()` / `tryFrom()` / `cases()` | native PHP enum behaviour |

## Organisation

Enums are grouped by domain, e.g. `Enlivy\Enums\Invoice\Statuses`,
`Enlivy\Enums\Payment\PaymentMethodProvider`,
`Enlivy\Enums\TenantBilling\BillingCycles`. Browse `src/Enums/` for the full
set. A selection relevant to recently added features:

| Enum | Values |
|------|--------|
| `EventDelivery\DestinationType` | `webhook`, `slack` |
| `EventDelivery\DeliveryStatus` | `pending`, `success`, `failed`, `dropped`, `anomaly` |
| `EventDelivery\TriggerEvent` | dotted event names (`invoice.paid`, `contract.all_parties_signed`, …) |
| `EventTrail\EventType` | event-trail subject change types |
| `EventTrail\Origin` | `back_office`, `client_portal`, `cron`, `webhook`, `system` |
| `Organization\ButtonStyles` | `primary`, `secondary`, `outline` |
| `TenantBilling\TrialChangeSetTypes` | `add`, `drop` |
| `TenantBilling\BillingEffects` | `prorated_now`, `trial`, `next_cycle`, `none` |
| `BillingPackage\BillingEffect` | `now`, `next_cycle` |
| `BillingPackage\SubscriptionTermStatuses` | `active`, `archived` |
| `BillingPackage\ProrationPolicy` | `none`, `prorate_immediately`, `prorate_next_invoice` |
| `BillingSchedule\PhaseFrequency` | `weekly`, `biweekly`, `monthly`, `every_3_months`, `every_6_months`, `yearly` |
| `BillingSchedule\Statuses` | `pending`, `active`, `payment_method_required`, `paused`, `completed`, `cancelled` |

> `Proposal\PaymentMethodKind` cases are now `BANK_TRANSFER` (`bank_transfer`)
> and `CARD` (`card`).

## Stability

These mirror the API contract and may gain cases as the API evolves. Always
handle unknown values defensively (`tryFrom()` returns `null` rather than
throwing) — a newer API may return a case an older SDK build does not know.
