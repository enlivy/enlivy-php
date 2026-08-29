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
| `BillingSchedule\Statuses` | `pending`, `active`, `payment_method_required`, `payment_failed`, `paused`, `completed`, `cancelled` |
| `BillingSchedule\InvoiceIssueTrigger` | `on_generation`, `on_payment` |
| `Payment\RefundStatus` | `succeeded`, `failed`, `pending` |
| `BillingPackage\ContractSectionContentSources` | `standard`, `reusable_content`, `purchase_items`, `purchase_terms`, `purchase_summary`, `product_list`, `purchased_product_list` |
| `CurrencyExchangeRateProviders` | `ecb`, `bnr`, `nbp`, `cnb`, `mnb`, `riksbank`, `dn` |
| `ExportData\Types` | `full`, `accounting_saga` |
| `NetworkExchange\DocumentTypeCodes` | `380` (commercial invoice), `381` (credit note) |
| `Invoice\NotificationLogTypes` | `network_exchange_auto_push`, `email`, `email_auto_send`, `email_reminder_upcoming`, `email_reminder_overdue` |
| `Organization\SettingGroups` | `invoicing`, `invoice_payment_reminder`, `taxes`, `receipts`, `banking`, `contracts`, `sales`, `users`, `blocked_identifiers`, `email`, `stripe_connect`, `network_exchange`, `network_exchange_auto_push`, `personalization` |
| `Organization\Environments` | `live`, `sandbox` |
| `Import\StopReasons` | `usage_limit`, `ai_limit`, `consecutive_failures`, `file_unreadable` |
| `BlockedIdentifier\Types` | `email`, `email_domain`, `phone_number` |
| `BlockedIdentifier\Sources` | `organization`, `platform`, `all` |
| `BankTransaction\States` | `backlog`, `completed`, `unbalanced`, `trashed` |
| `Receipt\Directions` | `inbound`, `outbound` |
| `Receipt\Sources` | `uploaded`, `generated` |
| `BillingPackage\PortalDiscoveryMode` | `disabled`, `request`, `checkout` |
| `BillingPackage\OutcomeMode` | `sale`, `funding`, `agreement` |
| `BillingPackage\TierPriceType` | `fixed`, `percent_of_baseline` |
| `BillingPackage\ContractPartySelections` | `standard`, `custom` |
| `BillingPackage\ContractPartySources` | `sender`, `receiver`, `assigned`, `stated` |
| `BillingPackage\ExchangeRateGuarantees` | `invoice`, `acceptance` |
| `Contract\PartyIdentityRequirements` | `contact`, `identity_document`, `civil_registry` |
| `Proposal\Stages` | `drafting`, `awaiting_acceptance`, `awaiting_contract`, `awaiting_signature`, `awaiting_payment`, `closed`, `rejected`, `expired` |
| `Proposal\StageActors` | `organization`, `customer`, `third_party`, `several` |
| `Proposal\NotificationLogTypes` | `email`, `email_seller_viewed`, `email_seller_accepted`, `email_seller_rejected`, `email_seller_expired`, `email_seller_contract_generated` |

> `BlockedIdentifier\Sources::ALL` is a filter directive on the list endpoint,
> not a value a stored row carries — a row is always `organization` or `platform`.

> `Import\StopReasons` describes why a [data import](organization/data-imports.md)
> stopped short of the end of its file. Only `file_unreadable` cannot be resumed;
> read `summary_json.is_resumable` rather than testing the reason yourself.

> `Proposal\PaymentMethodKind` cases are now `BANK_TRANSFER` (`bank_transfer`)
> and `CARD` (`card`).

> `BillingSchedule\Statuses` dropped `subscription_required` and `cancelling` in
> 2.7.0 — the API no longer sends either, and neither ever reached a production
> row. `cancelling` was a second spelling of `cancel_effective_at`, so a schedule
> the customer has asked to end stays `active` until it reaches `cancelled`.
> `subscription_required` stamped the organization's entitlement onto its schedule
> rows and has no replacement — the payments cron reads that entitlement directly.
> `payment_failed` is new: a schedule whose card keeps refusing stops minting
> cycles. See [UPGRADING](../UPGRADING.md).

> `Proposal\Stages` is where a proposal sits across itself, its contracts and
> their parties — `status` only records what the row was last set to, so two
> accepted proposals can report different stages. `closed`, `rejected` and
> `expired` are terminal. `Proposal\StageActors` names whose move it is;
> `several` covers both a step either side may take and one that more than one
> side owes.

> `BankTransaction\States` collapsed four cases into two in 3.0.0. A transaction
> is `completed` once it is fully settled against the documents it is connected to
> and `unbalanced` while it is not — the old `classified`, `connected`,
> `connected_partially` and `danger` reported how far along that reconciliation
> was, which is a property of the connections and not of the transaction. Filter
> on `state`, or read `is_connected`, rather than pinning the intermediate steps.
> See [UPGRADING](../UPGRADING.md).

> `BillingPackage\ExchangeRateGuarantees` picks when the rate for a proposal
> billed in a second currency is fixed: `invoice` re-quotes at issue time,
> `acceptance` freezes the figures the customer accepted.

> `Contract\PartyIdentityRequirements` is cumulative: `contact` needs somewhere
> to write to, `identity_document` adds the party's identity document, and
> `civil_registry` adds the civil-registry data a company registry asks for.
> Which fields each tier means is per-country. A company party carries no birth
> data, so `civil_registry` asks the same of it as `identity_document` does.

> `Proposal\NotificationLogTypes` distinguishes the proposal sent to a customer
> (`email`) from the lifecycle notices reported back to the organization (the
> `email_seller_*` cases). Rows carry `is_seller_notification` so you do not have
> to test the prefix yourself.

> `BillingPackage\OutcomeMode` decides whether a proposal built from the package
> ever produces a fiscal document: only `sale` does. `funding` (share subscriptions,
> loans, grants) and `agreement` (NDAs, framework agreements) settle without an
> invoice, so the charge pipeline never issues one.

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
