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
use Enlivy\Enums\Payment\PaymentProvider;

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
PaymentProvider::values();   // ['stripe', 'paypal']
PaymentProvider::names();    // ['STRIPE', 'PAYPAL']
PaymentProvider::cases();    // native PHP enum cases
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
`Enlivy\Enums\Payment\PaymentProvider`,
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
| `BillingSchedule\Statuses` | `pending`, `active`, `payment_method_required`, `paused`, `cancelling`, `completed`, `cancelled` |
| `Payment\RefundStatus` | `succeeded`, `failed`, `pending` |
| `BillingPackage\ContractSectionContentSources` | `standard`, `reusable_content`, `purchase_items`, `purchase_terms`, `purchase_summary`, `product_list`, `purchased_product_list` |
| `CurrencyExchangeRateProviders` | `ecb`, `bnr`, `nbp`, `cnb`, `mnb`, `riksbank`, `dn` |
| `ExportData\Types` | `full`, `accounting_saga` |
| `NetworkExchange\DocumentTypeCodes` | `380` (commercial invoice), `381` (credit note) |
| `Invoice\NotificationLogTypes` | `network_exchange_auto_push`, `email`, `email_auto_send`, `email_reminder_upcoming`, `email_reminder_overdue` |
| `Organization\SettingGroups` | `invoicing`, `invoice_payment_reminder`, `taxes`, `receipts`, `banking`, `contracts`, `sales`, `users`, `email`, `stripe_connect`, `network_exchange`, `network_exchange_auto_push`, `personalization` |

> `Proposal\PaymentMethodKind` cases are now `BANK_TRANSFER` (`bank_transfer`)
> and `CARD` (`card`).

## Tax

The tax-compliance subsystem ships a family of enums under `Enlivy\Enums\Tax\`:

| Enum | Values |
|------|--------|
| `Tax\TaxFamilies` | `vat`, `sales_tax`, `income_tax`, `payroll` |
| `Tax\ProductTaxCategories` | `general_services`, `digital_services`, `general_physical_goods`, `foodstuffs`, `printed_books_periodicals`, … (14 EU/UK/CH categories) |
| `Tax\RegistrationSchemes` | `vat_registered`, `small_business_domestic`, `small_business_cross_border`, `oss_union`, `oss_non_union`, `ioss`, `not_registered`, `micro_enterprise`, `profit_tax`, `self_employed_income`, `employer` |
| `Tax\SellerVatStatuses` | `undeclared`, `registered`, `small_business_exempt`, `not_registered`, `not_applicable` |
| `Tax\TaxApplicabilityReasons` | `seller_not_registered`, `outside_scope`, `domestic`, `eu_reverse_charge`, `eu_business_without_vat_id`, `eu_consumer` |
| `Tax\ValidationSources` | `vies`, `anaf`, `manual`, `companies_api` |
| `Tax\RegistrationSuggestionConfidences` | `verified`, `stored_identifier`, `derived`, `country_default` |
| `Tax\RegistrationSuggestionSources` | `companies_api`, `organization_information`, `country_pack`, `activity` |
| `Tax\FilingFrequencies` | `monthly`, `quarterly`, `semiannual`, `annual`, `event_driven` |
| `Tax\FilingPeriodStatuses` | `open`, `closed`, `filed`, `submitted` |
| `Tax\FilingPeriodPaymentTypes` | `payment`, `refund`, `advance`, `penalty`, `interest` |
| `Tax\FilingPeriodPaymentStatuses` | `pending`, `cleared`, `failed` |
| `Tax\AssuranceModes` | `full_books`, `hybrid`, `declared`, `none` |
| `Tax\TaxEventDirections` | `output`, `input` |
| `Tax\TaxEventSourceTypes` | `invoice`, `receipt`, `customs`, `bank_correction`, `authority`, `manual`, `baseline` |
| `Tax\TaxEventRegimes` | `accrual`, `cash` |
| `Tax\TaxEventSupplyTypes` | `goods`, `services`, `triangular` |

## Stability

These mirror the API contract and may gain cases as the API evolves. Always
handle unknown values defensively (`tryFrom()` returns `null` rather than
throwing) — a newer API may return a case an older SDK build does not know.
