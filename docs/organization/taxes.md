# Taxes

Configure tax classes, rates, and types for proper tax calculation on invoices. The tax system supports EU VAT requirements, location-based rates, and PEPPOL e-invoicing compliance.

## Key Concepts

### Tax Hierarchy

```
Tax Type (e.g., "VAT", "Sales Tax")
    |
Tax Class (e.g., "Standard", "Reduced", "Zero")
    |
Tax Rate (e.g., 19% with specific rules and locations)
```

### EU VAT Support

Tax rates support EU VAT properties including:
- EU VAT class (standard, reduced, super-reduced, zero)
- VAT exemption with PEPPOL VATEX codes
- Business entity and EU VAT registration conditions

## Tax Classes

Tax classes group tax rates for different product categories.

### Create Tax Class

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$taxClass = $client->taxClasses->create([
    // Multilingual name
    'name_lang_map' => [
        'en' => 'Standard Rate',
        'ro' => 'Cota Standard',
    ],

    // Multilingual description (optional)
    'description_lang_map' => [
        'en' => 'Standard VAT rate for most goods and services',
        'ro' => 'Cota TVA standard pentru majoritatea bunurilor si serviciilor',
    ],
]);

echo "Created tax class: {$taxClass->id}\n";
```

### List Tax Classes

```php
<?php

$taxClasses = $client->taxClasses->list();

foreach ($taxClasses as $tc) {
    $name = $tc->name_lang_map['en'] ?? array_values($tc->name_lang_map)[0] ?? 'Unnamed';
    echo "- {$name}\n";
}
```

### Retrieve Tax Class

```php
<?php

$taxClass = $client->taxClasses->retrieve('org_tax_class_xxx', [
    'include' => ['organization'],
]);

foreach ($taxClass->name_lang_map as $lang => $name) {
    echo "Name [{$lang}]: {$name}\n";
}
```

## Tax Rates

Tax rates define the actual percentage applied, with support for conditions and locations.

### Create Basic Tax Rate

```php
<?php

$taxRate = $client->taxRates->create([
    // Required fields
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'Standard VAT 19%',
    'rate' => 19.00,

    // Required boolean flags
    'has_eu_vat_properties' => true,
    'is_compound' => false,  // Compound tax is calculated on subtotal + other taxes
    'is_shipping' => false,  // Whether this rate applies to shipping
    'is_inclusive' => false, // Whether prices already include this tax

    // Optional display name
    'display_name' => 'VAT',
]);

echo "Created tax rate: {$taxRate->id}\n";
```

### Create Tax Rate with EU VAT Properties

```php
<?php

$taxRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'Standard VAT',
    'rate' => 19.00,

    // EU VAT settings
    'has_eu_vat_properties' => true,
    'eu_vat_class' => 'standard', // standard, reduced, super_reduced, zero

    // Business entity conditions
    'is_business_entity' => false,     // Only applies to non-business customers
    'is_eu_vat_registered' => false,   // Only applies to non-VAT-registered

    // Tax calculation settings
    'is_compound' => false,
    'is_shipping' => false,
    'is_inclusive' => false,

    // Priority for rate selection (lower = higher priority)
    'priority' => 0,
]);
```

### Create VAT Exempt Rate

```php
<?php

// For VAT exempt supplies (requires vatex_code)
$exemptRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'Zero Rate - Export',
    'rate' => 0.00,

    'has_eu_vat_properties' => true,
    'eu_vat_class' => 'zero',
    'is_vat_exempt' => true,

    // PEPPOL VATEX code (required when is_vat_exempt is true)
    'vatex_code' => 'vatex-eu-g', // Exempt: Export outside the EU

    'is_compound' => false,
    'is_shipping' => false,
    'is_inclusive' => false,
]);
```

### Create Tax Rate with Locations

For location-specific tax rates (e.g., state/regional taxes):

```php
<?php

$taxRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'California Sales Tax',
    'rate' => 7.25,

    'has_eu_vat_properties' => false,
    'is_compound' => false,
    'is_shipping' => true,
    'is_inclusive' => false,
    'has_locations' => true,

    // Location-specific rules
    'locations' => [
        [
            'country_code' => 'US',
            'iso_3166' => 'US-CA', // California
            'zip_code' => null,    // All ZIP codes
        ],
    ],
]);
```

### Create Tax Rate with Multiple Locations

```php
<?php

$taxRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'name' => 'EU Standard VAT',
    'rate' => 19.00,

    'has_eu_vat_properties' => true,
    'eu_vat_class' => 'standard',
    'is_compound' => false,
    'is_shipping' => false,
    'is_inclusive' => false,
    'has_locations' => true,

    'locations' => [
        ['country_code' => 'RO', 'iso_3166' => null, 'zip_code' => null],
        ['country_code' => 'DE', 'iso_3166' => null, 'zip_code' => null],
        ['country_code' => 'FR', 'iso_3166' => null, 'zip_code' => null],
    ],
]);
```

### List Tax Rates

```php
<?php

$taxRates = $client->taxRates->list([
    'include' => ['organizationTaxClass', 'locations'],
]);

foreach ($taxRates as $rate) {
    echo "{$rate->name}: {$rate->rate}%\n";

    if ($rate->has_locations && !empty($rate->locations)) {
        foreach ($rate->locations as $location) {
            echo "  - {$location->country_code}";
            if ($location->iso_3166) {
                echo " ({$location->iso_3166})";
            }
            echo "\n";
        }
    }
}
```

### Retrieve Tax Rate

```php
<?php

$taxRate = $client->taxRates->retrieve('org_tax_rate_xxx', [
    'include' => ['organizationTaxClass', 'locations'],
]);

echo "Name: {$taxRate->name}\n";
echo "Rate: {$taxRate->rate}%\n";
echo "Compound: " . ($taxRate->is_compound ? 'Yes' : 'No') . "\n";
echo "Inclusive: " . ($taxRate->is_inclusive ? 'Yes' : 'No') . "\n";

if ($taxRate->has_eu_vat_properties) {
    echo "EU VAT Class: {$taxRate->eu_vat_class}\n";
    if ($taxRate->is_vat_exempt) {
        echo "VATEX Code: {$taxRate->vatex_code}\n";
    }
}
```

## Tax Types

Tax types categorize different kinds of taxes (VAT, sales tax, etc.).

### Create Tax Type

```php
<?php

$taxType = $client->taxTypes->create([
    // Required fields
    'alias' => 'vat', // Unique identifier within organization

    // Multilingual name
    'name_lang_map' => [
        'en' => 'VAT',
        'ro' => 'TVA',
    ],

    // Multilingual note (optional)
    'note_lang_map' => [
        'en' => 'Value Added Tax',
        'ro' => 'Taxa pe Valoarea Adaugata',
    ],

    // Active status (optional)
    'is_active' => true,
]);

echo "Created tax type: {$taxType->id}\n";
```

### List Tax Types

```php
<?php

$taxTypes = $client->taxTypes->list();

foreach ($taxTypes as $type) {
    $name = $type->name_lang_map['en'] ?? $type->alias;
    $status = $type->is_active ? 'active' : 'inactive';
    echo "{$type->alias}: {$name} ({$status})\n";
}
```

## Tax Filing Jurisdictions

For businesses operating in multiple tax jurisdictions.

### Create Tax Filing Jurisdiction

```php
<?php

$jurisdiction = $client->taxFilingJurisdictions->create([
    // Required fields
    'jurisdiction_code' => 'RO',  // Unique code (max 10 chars)
    'jurisdiction_name' => 'Romania', // Unique name

    // Multilingual note (optional)
    'note_lang_map' => [
        'en' => 'Romanian tax filing jurisdiction - ANAF',
    ],

    // Active status (optional)
    'is_active' => true,
]);

echo "Created jurisdiction: {$jurisdiction->id}\n";
```

### List Tax Filing Jurisdictions

```php
<?php

$jurisdictions = $client->taxFilingJurisdictions->list();

foreach ($jurisdictions as $j) {
    $status = $j->is_active ? 'active' : 'inactive';
    echo "{$j->jurisdiction_code}: {$j->jurisdiction_name} ({$status})\n";
}
```

## Complete Example: Tax Setup

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create tax type
    $vatType = $client->taxTypes->create([
        'alias' => 'vat',
        'name_lang_map' => [
            'en' => 'VAT',
            'ro' => 'TVA',
        ],
        'note_lang_map' => [
            'en' => 'Value Added Tax',
        ],
        'is_active' => true,
    ]);

    // 2. Create tax classes
    $standardClass = $client->taxClasses->create([
        'name_lang_map' => [
            'en' => 'Standard',
            'ro' => 'Standard',
        ],
    ]);

    $reducedClass = $client->taxClasses->create([
        'name_lang_map' => [
            'en' => 'Reduced',
            'ro' => 'Redusa',
        ],
    ]);

    $zeroClass = $client->taxClasses->create([
        'name_lang_map' => [
            'en' => 'Zero Rate',
            'ro' => 'Cota Zero',
        ],
    ]);

    // 3. Create tax rates
    $client->taxRates->create([
        'organization_tax_class_id' => $standardClass->id,
        'name' => 'Standard VAT 19%',
        'display_name' => 'VAT',
        'rate' => 19.00,
        'has_eu_vat_properties' => true,
        'eu_vat_class' => 'standard',
        'is_compound' => false,
        'is_shipping' => false,
        'is_inclusive' => false,
    ]);

    $client->taxRates->create([
        'organization_tax_class_id' => $reducedClass->id,
        'name' => 'Reduced VAT 9%',
        'display_name' => 'VAT',
        'rate' => 9.00,
        'has_eu_vat_properties' => true,
        'eu_vat_class' => 'reduced',
        'is_compound' => false,
        'is_shipping' => false,
        'is_inclusive' => false,
    ]);

    $client->taxRates->create([
        'organization_tax_class_id' => $zeroClass->id,
        'name' => 'Zero Rate VAT',
        'display_name' => 'VAT',
        'rate' => 0.00,
        'has_eu_vat_properties' => true,
        'eu_vat_class' => 'zero',
        'is_vat_exempt' => true,
        'vatex_code' => 'vatex-eu-g', // Export exempt
        'is_compound' => false,
        'is_shipping' => false,
        'is_inclusive' => false,
    ]);

    // 4. Create filing jurisdiction
    $client->taxFilingJurisdictions->create([
        'jurisdiction_code' => 'RO',
        'jurisdiction_name' => 'Romania',
        'note_lang_map' => [
            'en' => 'Romanian tax authority - ANAF',
        ],
        'is_active' => true,
    ]);

    echo "Tax configuration complete!\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
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
            'organization_tax_class_id' => $standardClass->id, // 19% VAT
        ],
        [
            'name_lang_map' => ['en' => 'Books'],
            'quantity' => 5,
            'price' => 20.00,
            'type' => 'physical',
            'organization_tax_class_id' => $reducedClass->id, // 9% VAT
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

### Tax Rate Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `organization_tax_class_id` | string | Yes | Tax class ID |
| `name` | string | Yes | Rate name |
| `rate` | numeric | Yes | Tax rate percentage |
| `has_eu_vat_properties` | boolean | Yes | Whether EU VAT rules apply |
| `is_compound` | boolean | Yes | Calculated on subtotal + other taxes |
| `is_shipping` | boolean | Yes | Applies to shipping |
| `is_inclusive` | boolean | Yes | Prices include this tax |
| `display_name` | string | No | Display name on invoices |
| `eu_vat_class` | string | No | EU VAT class (standard/reduced/super_reduced/zero) |
| `is_vat_exempt` | boolean | No | VAT exempt supply |
| `vatex_code` | string | No* | PEPPOL VATEX code (*required if is_vat_exempt) |
| `is_business_entity` | boolean | No | Only for business entities |
| `is_eu_vat_registered` | boolean | No | Only for EU VAT registered |
| `has_locations` | boolean | No | Has location-specific rules |
| `locations` | array | No | Location rules |
| `priority` | integer | No | Rate selection priority |

### Tax Rate Location Fields

| Field | Type | Description |
|-------|------|-------------|
| `country_code` | string | ISO 3166-1 alpha-2 country code |
| `iso_3166` | string | ISO 3166-2 subdivision code |
| `zip_code` | string | ZIP/postal code |

### Tax Type Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `alias` | string | Yes | Unique identifier (max 50 chars) |
| `name_lang_map` | object | Yes | Name by language |
| `note_lang_map` | object | No | Note by language |
| `is_active` | boolean | No | Active status |

### Tax Filing Jurisdiction Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `jurisdiction_code` | string | Yes | Unique code (max 10 chars) |
| `jurisdiction_name` | string | Yes | Unique jurisdiction name |
| `note_lang_map` | object | No | Note by language |
| `is_active` | boolean | No | Active status |

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

- [Products](products.md) - Assign tax classes to products
- [Invoices](invoices.md) - Tax calculation on invoices
