# Taxes

Configure tax classes, rates, and types for tax calculation on invoices, and —
with the compliance engine — track a tax subledger, jurisdiction registrations,
and filing periods. Supports EU VAT, location-based rates, and PEPPOL
e-invoicing compliance.

## Key Concepts

### Tax hierarchy

```
Tax Type (e.g. "VAT", "Sales Tax")
    |
Tax Class (e.g. "Standard", "Reduced", "Zero"; carries a tax category)
    |
Tax Rate (e.g. 19% with specific rules and locations)
```

### Compliance engine

On top of configuration, the SDK exposes the tax-compliance surface:

- **Tax registrations** — how you are registered in each country/subdivision
  (VAT, OSS/IOSS, small-business schemes).
- **Tax events** — the tax subledger: one row per tax-relevant supply, input or
  output, feeding the filing figures.
- **Tax filing periods** — per-jurisdiction periods with computed vs declared
  figures, plus the payments recorded against them.
- **Tax monitors** — registration/nexus threshold snapshots.

### EU VAT support

Tax rates support EU VAT properties including the EU VAT class (standard,
reduced, super-reduced, zero), VAT exemption with PEPPOL VATEX codes, and
business-entity / EU-VAT-registration conditions.

## Tax Classes

Tax classes group tax rates and carry a product tax category used to pick the
right rate per jurisdiction.

### Create Tax Class

```php
<?php

use Enlivy\EnlivyClient;
use Enlivy\Enums\Tax\ProductTaxCategories;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$taxClass = $client->taxClasses->create([
    'name_lang_map' => [
        'en' => 'Standard Rate',
        'ro' => 'Cota Standard',
    ],
    'description_lang_map' => [
        'en' => 'Standard VAT rate for most goods and services',
    ],

    // Display name shown on documents (optional; localizable)
    'display_name_lang_map' => [
        'en' => 'VAT',
    ],

    // Product tax category — drives jurisdiction rate selection (optional)
    'tax_category' => ProductTaxCategories::GENERAL_SERVICES->value,
]);

echo "Created tax class: {$taxClass->id}\n";
```

### List / Retrieve Tax Classes

```php
<?php

$taxClasses = $client->taxClasses->list([
    'include' => ['tax_rates_overview'],
]);

foreach ($taxClasses as $tc) {
    $name = $tc->name_lang_map['en'] ?? array_values($tc->name_lang_map ?? [])[0] ?? 'Unnamed';
    echo "- {$name} ({$tc->tax_category})\n";
}

$taxClass = $client->taxClasses->retrieve('org_tax_class_xxx', [
    'include' => ['organization'],
]);
```

## Tax Rates

Tax rates define the percentage applied, with support for conditions and
locations. Names and display names are localizable.

### Create Basic Tax Rate

```php
<?php

$taxRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'Standard VAT 19%',
    'rate' => 19.00,

    // Whether EU VAT rules apply to this rate
    'has_eu_vat_properties' => true,

    // Calculation flags
    'is_compound' => false,  // Calculated on subtotal + other taxes
    'is_inclusive' => false, // Whether prices already include this tax

    // Display name on documents (optional; use display_name_lang_map to localize)
    'display_name' => 'VAT',
]);

echo "Created tax rate: {$taxRate->id}\n";
```

### Create Tax Rate with EU VAT Properties

```php
<?php

$taxRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name_lang_map' => ['en' => 'Standard VAT'],
    'rate' => 19.00,

    'has_eu_vat_properties' => true,
    'eu_vat_class' => 'standard', // standard, reduced, super_reduced, zero

    // Conditions
    'is_business_entity' => false,   // Only applies to non-business customers
    'is_eu_vat_registered' => false, // Only applies to non-VAT-registered

    'is_compound' => false,
    'is_inclusive' => false,

    // Priority for rate selection (lower = higher priority)
    'priority' => 0,
]);
```

### Create VAT Exempt Rate

```php
<?php

$exemptRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'Zero Rate - Export',
    'rate' => 0.00,

    'has_eu_vat_properties' => true,
    'eu_vat_class' => 'zero',
    'is_vat_exempt' => true,

    // PEPPOL VATEX code (required when is_vat_exempt is true)
    'vatex_code' => 'vatex-eu-g', // Export outside the EU

    'is_compound' => false,
    'is_inclusive' => false,
]);
```

### Create Tax Rate with Locations

For location-specific rates (e.g. state/regional taxes), set `has_locations`
and pass a `locations` collection. Each location is an upsert keyed on `id`
(omit `id` to create, include it to update, add `_deleted => true` to remove).

```php
<?php

$taxRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'EU Standard VAT',
    'rate' => 19.00,

    'has_eu_vat_properties' => true,
    'eu_vat_class' => 'standard',
    'is_compound' => false,
    'is_inclusive' => false,
    'has_locations' => true,

    'locations' => [
        ['country_code' => 'RO', 'zip_code' => null],
        ['country_code' => 'DE', 'zip_code' => null],
        ['country_code' => 'FR', 'zip_code' => null],
    ],
]);
```

### List / Retrieve Tax Rates

```php
<?php

$taxRates = $client->taxRates->list([
    'include' => ['organization_tax_class', 'locations'],
    'organization_tax_class_id' => 'org_tax_class_xxx', // filter
]);

foreach ($taxRates as $rate) {
    echo "{$rate->name}: {$rate->rate}%\n";

    if ($rate->has_locations && !empty($rate->locations)) {
        foreach ($rate->locations as $location) {
            echo "  - {$location->country_code}\n";
        }
    }
}
```

## Tax Types

Tax types categorize different kinds of taxes (VAT, sales tax, etc.).

```php
<?php

$taxType = $client->taxTypes->create([
    'alias' => 'vat', // Unique identifier within organization
    'name_lang_map' => ['en' => 'VAT', 'ro' => 'TVA'],
    'note_lang_map' => ['en' => 'Value Added Tax'],
    'is_active' => true,
]);
```

## Tax Filing Jurisdictions

For businesses operating in multiple tax jurisdictions.

```php
<?php

$jurisdiction = $client->taxFilingJurisdictions->create([
    'jurisdiction_code' => 'RO',       // Unique code (max 10 chars)
    'jurisdiction_name' => 'Romania',  // Unique name
    'note_lang_map' => ['en' => 'Romanian tax authority - ANAF'],
    'is_active' => true,
]);
```

## Tax Registrations

Where and how the organization is registered for tax. `suggested()` returns
system-derived registration suggestions from the organization profile and
transaction footprint (raw suggestion rows, not persisted records).

```php
<?php

use Enlivy\Enums\Tax\RegistrationSchemes;
use Enlivy\Enums\Tax\FilingFrequencies;

$registration = $client->taxRegistrations->create([
    'country_code' => 'RO',
    'scheme' => RegistrationSchemes::VAT_REGISTERED->value,
    'registration_number' => 'RO12345678',
    'filing_frequency' => FilingFrequencies::MONTHLY->value,
    'effective_from' => '2026-01-01',

    // Optional
    'subdivision_iso_3166' => null,
    'exemption_vatex_code' => null,
    'exemption_reason_lang_map' => null,
]);

// Filter by country / scheme
$registrations = $client->taxRegistrations->list([
    'country_code' => 'RO',
    'scheme' => RegistrationSchemes::VAT_REGISTERED->value,
]);

// System-suggested registrations
$suggested = $client->taxRegistrations->suggested();
```

## Tax Events (subledger)

Each tax event is one tax-relevant supply (input or output). Amounts are signed
decimals in major currency units.

```php
<?php

use Enlivy\Enums\Tax\TaxEventDirections;
use Enlivy\Enums\Tax\TaxEventSourceTypes;
use Enlivy\Enums\Tax\TaxEventSupplyTypes;

$event = $client->taxEvents->create([
    'direction' => TaxEventDirections::OUTPUT->value,
    'source_type' => TaxEventSourceTypes::MANUAL->value, // create-only
    'tax_point_date' => '2026-07-15',
    'base_amount' => 1000.00,
    'tax_amount' => 190.00,

    // Optional context
    'country_code' => 'RO',
    'category' => 'S',                 // EU VAT class
    'rate' => 19.00,
    'currency' => 'RON',
    'supply_type' => TaxEventSupplyTypes::SERVICES->value,
    'is_reverse_charge' => false,
    'counterparty_name' => 'Acme SRL',
    'counterparty_country_code' => 'RO',
    'counterparty_vat_number' => 'RO87654321',
]);

// Filter events
$events = $client->taxEvents->list([
    'direction' => TaxEventDirections::OUTPUT->value,
    'organization_tax_filing_period_id' => 'org_tax_filing_period_xxx',
    'tax_point_date_from' => '2026-07-01',
    'tax_point_date_to' => '2026-07-31',
    'include' => ['tax_filing_period', 'tax_type'],
]);
```

`source_type` is set only at creation. Events, like the other tax records,
support soft delete and `restore()`.

## Tax Filing Periods

Per-jurisdiction filing periods. The server computes figures from the subledger;
`acceptComputed()` adopts them as the declared figures (only while the period is
open), and `returnView()` returns the mapped tax-return box figures.

```php
<?php

use Enlivy\Enums\Tax\FilingPeriodStatuses;

$period = $client->taxFilingPeriods->create([
    'organization_tax_filing_jurisdiction_id' => 'org_tax_filing_jurisdiction_xxx',
    'organization_tax_type_id' => 'org_tax_type_xxx',
    'period_start' => '2026-07-01',
    'period_end' => '2026-07-31',

    // Optional declared figures (else computed)
    'currency' => 'RON',
    'status' => FilingPeriodStatuses::OPEN->value,
]);

$period = $client->taxFilingPeriods->retrieve('org_tax_filing_period_xxx', [
    'include' => ['payments', 'tax_registration', 'tax_type'],
]);

// Adopt the server-computed figures as declared
$period = $client->taxFilingPeriods->acceptComputed('org_tax_filing_period_xxx');

// The mapped return-box figures (raw payload; pass return_key to pick a return)
$boxes = $client->taxFilingPeriods->returnView('org_tax_filing_period_xxx');

// Filter
$periods = $client->taxFilingPeriods->list([
    'status' => FilingPeriodStatuses::OPEN->value,
    'organization_tax_filing_jurisdiction_id' => 'org_tax_filing_jurisdiction_xxx',
    'period_start_from' => '2026-01-01',
    'is_liability_cleared' => false,
]);
```

### Filing Period Payments

Payments are nested under a filing period — every method takes the filing-period
id first.

```php
<?php

use Enlivy\Enums\Tax\FilingPeriodPaymentTypes;

$payment = $client->taxFilingPeriodPayments->create('org_tax_filing_period_xxx', [
    'payment_type' => FilingPeriodPaymentTypes::PAYMENT->value,
    'payment_date' => '2026-08-25',
    'amount' => 190.00,
    'currency' => 'RON',

    // When payment_method is bank_transfer, a bank account is required
    'payment_method' => 'bank_transfer',
    'organization_bank_account_id' => 'org_bank_acc_xxx',
]);

$payments = $client->taxFilingPeriodPayments->list('org_tax_filing_period_xxx', [
    'status' => 'cleared',
    'payment_date_from' => '2026-08-01',
]);

$client->taxFilingPeriodPayments->delete('org_tax_filing_period_xxx', $payment->id);
```

## Will Tax Be Charged?

Determine up front whether a sale to a given recipient will carry tax — useful when
building a quote or a checkout. Pass an existing recipient by id, or describe one ad-hoc:

```php
<?php

// By recipient
$result = $client->misc->determineIsTaxCharged([
    'organization_receiver_user_id' => 'org_user_xxx',
]);

// Or ad-hoc (any combination; all optional)
$result = $client->misc->determineIsTaxCharged([
    'country_code' => 'DE',
    'is_business_entity' => true,
    'is_eu_vat_registered' => true,
]);

$result->is_tax_charged;   // bool
$result->reason;           // Tax\TaxApplicabilityReasons value, e.g. eu_reverse_charge
$result->needs_attention;  // bool — the seller's setup may need review (e.g. not registered)
```

`reason` explains the outcome — `domestic`, `eu_reverse_charge`, `eu_consumer`,
`eu_business_without_vat_id`, `outside_scope` or `seller_not_registered` (see
[enums](../enums.md)).

## Tax Monitors

A snapshot of registration/nexus threshold monitors for the organization (raw
payload).

```php
<?php

$monitors = $client->misc->taxMonitors();
```

## Using Tax Classes in Invoices

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',
    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Consulting'],
            'quantity' => 10,
            'price' => 100.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_class_standard', // 19% VAT
        ],
    ],
]);

echo "Subtotal: {$invoice->sub_total}\n";
echo "Tax: {$invoice->tax_total}\n";
echo "Total: {$invoice->total}\n";
```

## Field Reference

### Tax Class Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name_lang_map` | object | No | Name by language |
| `description_lang_map` | object | No | Description by language |
| `display_name` / `display_name_lang_map` | string / object | No | Display name on documents |
| `tax_category` | string | No | Product tax category (`Enums\Tax\ProductTaxCategories`) |

### Tax Rate Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `organization_tax_class_id` | string | Yes | Tax class ID |
| `name` / `name_lang_map` | string / object | Yes | Rate name |
| `rate` | numeric | Yes | Tax rate percentage |
| `has_eu_vat_properties` | boolean | Yes | Whether EU VAT rules apply |
| `is_compound` | boolean | No | Calculated on subtotal + other taxes |
| `is_inclusive` | boolean | No | Prices include this tax |
| `display_name` / `display_name_lang_map` | string / object | No | Display name on invoices |
| `eu_vat_class` | string | No | EU VAT class (standard/reduced/super_reduced/zero) |
| `is_vat_exempt` | boolean | No | VAT exempt supply |
| `vatex_code` | string | No* | PEPPOL VATEX code (*required if is_vat_exempt) |
| `is_business_entity` | boolean | No | Only for business entities |
| `is_eu_vat_registered` | boolean | No | Only for EU VAT registered |
| `has_locations` | boolean | No | Has location-specific rules |
| `locations` | array | No | Location rules (`country_code`, `zip_code`) |
| `priority` | integer | No | Rate selection priority |

`seller_country_code`, `retired_at`, `stripe_tax_rate_id` and the
`auto_imported_*` fields are returned on the resource but are system-managed.

### Tax Registration Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `country_code` | string | Yes | Country of registration |
| `scheme` | string | Yes | Registration scheme (`Enums\Tax\RegistrationSchemes`) |
| `effective_from` | date | Yes | Effective start date |
| `subdivision_iso_3166` | string | No | Subdivision code |
| `registration_number` | string | No | Registration/VAT number |
| `filing_frequency` | string | No | `Enums\Tax\FilingFrequencies` |
| `effective_to` | date | No | Effective end date |
| `cash_accounting_from` / `cash_accounting_to` | date | No | Cash-accounting window |
| `exemption_vatex_code` | string | No | PEPPOL VATEX code |
| `exemption_reason_lang_map` | object | No | Exemption reason by language |

### Tax Event Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `direction` | string | Yes | `output` or `input` |
| `source_type` | string | Yes* | Source type (*create-only) |
| `tax_point_date` | date | Yes | Tax point date |
| `base_amount` | numeric | Yes | Net base amount (signed, major units) |
| `tax_amount` | numeric | Yes | Tax amount (signed, major units) |
| `country_code`, `scheme`, `regime`, `category`, `rate`, `currency` | mixed | No | Tax context |
| `is_reverse_charge`, `supply_type`, `deduction_rate`, `transaction_count` | mixed | No | Supply attributes |
| `counterparty_*` | mixed | No | Counterparty name / country / subdivision / VAT number / flags |

### Tax Filing Period Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `organization_tax_filing_jurisdiction_id` | string | Yes | Jurisdiction ID |
| `organization_tax_type_id` | string | Yes | Tax type ID |
| `period_start` / `period_end` | date | Yes | Period bounds |
| `tax_collectible`, `tax_paid_input`, `tax_withheld`, `adjustments`, `credit_applied_from_previous` | numeric | No | Declared figures |
| `filing_due_date`, `payment_due_date`, `currency`, `status` | mixed | No | Filing metadata |

### Tax Filing Period Payment Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `payment_type` | string | Yes | `Enums\Tax\FilingPeriodPaymentTypes` |
| `payment_date` | date | Yes | Payment date |
| `amount` | numeric | Yes | Amount (major units) |
| `payment_method` | string | No | Payment method |
| `organization_bank_account_id` | string | No* | Bank account (*required for bank transfers) |
| `organization_bank_transaction_id` | string | No | Linked bank transaction |
| `currency`, `reference`, `status` | mixed | No | Payment metadata |

### EU VAT Classes

| Value | Description |
|-------|-------------|
| `standard` | Standard rate (typically 19-25%) |
| `reduced` | Reduced rate (typically 5-10%) |
| `super_reduced` | Super-reduced rate (typically 0-5%) |
| `zero` | Zero rate |

### Common VATEX Codes

| Code | Description |
|------|-------------|
| `vatex-eu-ae` | Reverse charge |
| `vatex-eu-g` | Export outside the EU |
| `vatex-eu-ic` | Intra-community supply |
| `vatex-eu-o` | Not subject to VAT |

## Related

- [Products](products.md) — assign tax classes to products
- [Invoices](invoices.md) — tax calculation on invoices
- [Enums](../enums.md) — the full set of tax enums
