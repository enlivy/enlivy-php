# Taxes

Configure tax classes, rates, and types for proper tax calculation on invoices.

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
    'name' => 'Standard Rate',
    'description' => 'Standard VAT rate for most goods and services',
    'is_default' => true,
]);
```

### List Tax Classes

```php
<?php

$taxClasses = $client->taxClasses->list();

foreach ($taxClasses as $tc) {
    echo "{$tc->name}" . ($tc->is_default ? ' (default)' : '') . "\n";
}
```

## Tax Rates

Tax rates define the actual percentage applied.

### Create Tax Rate

```php
<?php

$taxRate = $client->taxRates->create([
    'organization_tax_class_id' => 'org_tax_class_xxx',
    'organization_tax_type_id' => 'org_tax_type_xxx',
    'rate' => 19.00, // 19%
    'country_code' => 'RO',
    'is_active' => true,
    'effective_from' => '2026-01-01',
]);
```

### List Tax Rates

```php
<?php

$taxRates = $client->taxRates->list([
    'filter' => ['is_active' => true],
    'include' => ['tax_class', 'tax_type'],
]);

foreach ($taxRates as $rate) {
    echo "{$rate->tax_class->name}: {$rate->rate}% ({$rate->tax_type->name})\n";
}
```

## Tax Types

Tax types categorize different kinds of taxes (VAT, sales tax, etc.).

### Create Tax Type

```php
<?php

$taxType = $client->taxTypes->create([
    'name' => 'VAT',
    'code' => 'VAT',
    'description' => 'Value Added Tax',
]);
```

### List Tax Types

```php
<?php

$taxTypes = $client->taxTypes->list();

foreach ($taxTypes as $type) {
    echo "{$type->code}: {$type->name}\n";
}
```

## Tax Filing Jurisdictions

For businesses operating in multiple jurisdictions.

```php
<?php

$jurisdictions = $client->taxFilingJurisdictions->list();

$jurisdiction = $client->taxFilingJurisdictions->create([
    'name' => 'Romania',
    'country_code' => 'RO',
    'tax_authority' => 'ANAF',
]);
```

## Complete Example: Tax Setup

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

// 1. Create tax type
$vatType = $client->taxTypes->create([
    'name' => 'VAT',
    'code' => 'VAT',
]);

// 2. Create tax classes
$standardClass = $client->taxClasses->create([
    'name' => 'Standard',
    'is_default' => true,
]);

$reducedClass = $client->taxClasses->create([
    'name' => 'Reduced',
    'is_default' => false,
]);

$zeroClass = $client->taxClasses->create([
    'name' => 'Zero Rate',
    'is_default' => false,
]);

// 3. Create tax rates
$client->taxRates->create([
    'organization_tax_class_id' => $standardClass->id,
    'organization_tax_type_id' => $vatType->id,
    'rate' => 19.00,
    'country_code' => 'RO',
    'is_active' => true,
]);

$client->taxRates->create([
    'organization_tax_class_id' => $reducedClass->id,
    'organization_tax_type_id' => $vatType->id,
    'rate' => 9.00,
    'country_code' => 'RO',
    'is_active' => true,
]);

$client->taxRates->create([
    'organization_tax_class_id' => $zeroClass->id,
    'organization_tax_type_id' => $vatType->id,
    'rate' => 0.00,
    'country_code' => 'RO',
    'is_active' => true,
]);

echo "Tax configuration complete!\n";
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

## Related

- [Products](products.md) - Assign tax classes to products
- [Invoices](invoices.md) - Tax calculation on invoices
