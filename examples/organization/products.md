# Products

Manage your product and service catalog. Products support multi-currency pricing, multilingual names and descriptions, and PEPPOL e-invoicing compliance.

## Key Concepts

### Multi-Currency Pricing

Products use a `price_map` object instead of a single price/currency:

```php
'price_map' => [
    'EUR' => 150.00,
    'USD' => 165.00,
    'RON' => 750.00,
]
```

When a product has multiple currencies, you must specify `primary_currency`.

### Product Types

| Type | Description |
|------|-------------|
| `service` | Services, consulting, labor |
| `digital` | Software, licenses, downloads |
| `physical` | Physical goods requiring shipping |
| `bonus` | Complimentary items, discounts |

## Creating Products

### Basic Service Product

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$service = $client->products->create([
    // Required fields
    'type' => 'service',
    'price_map' => [
        'EUR' => 150.00,
    ],

    // Multilingual name
    'name_lang_map' => [
        'en' => 'Consulting Hour',
        'ro' => 'Ora Consultanta',
    ],

    // Multilingual description (optional)
    'description_lang_map' => [
        'en' => 'One hour of professional consulting',
        'ro' => 'O ora de consultanta profesionala',
    ],

    // Multilingual unit (optional)
    'unit_lang_map' => [
        'en' => 'hour',
        'ro' => 'ora',
    ],

    // Tax configuration
    'organization_tax_class_id' => 'org_tax_xxx',
    'price_is_tax_inclusive' => false,

    // Unique identifier (optional)
    'alias' => 'consult-hour',

    // Enable for sale
    'is_sold' => true,
]);

echo "Created product: {$service->id}\n";
```

### Multi-Currency Product

```php
<?php

$product = $client->products->create([
    'type' => 'digital',
    'name_lang_map' => ['en' => 'Software License (Annual)'],

    // Multi-currency pricing
    'price_map' => [
        'EUR' => 299.00,
        'USD' => 329.00,
        'GBP' => 259.00,
    ],

    // Required when multiple currencies
    'primary_currency' => 'EUR',

    'organization_tax_class_id' => 'org_tax_xxx',
    'alias' => 'license-annual',
    'is_sold' => true,
]);
```

### Physical Product

```php
<?php

$physical = $client->products->create([
    'type' => 'physical',
    'name_lang_map' => ['en' => 'Laptop Stand'],
    'description_lang_map' => ['en' => 'Ergonomic aluminum laptop stand'],
    'unit_lang_map' => ['en' => 'piece'],
    'price_map' => [
        'EUR' => 49.99,
    ],
    'organization_tax_class_id' => 'org_tax_xxx',
    'alias' => 'stand-001',

    // Product identifiers (barcodes)
    'ean_number' => '5901234123457',  // EAN-13
    'upc_number' => '012345678905',   // UPC-A

    'is_sold' => true,
]);
```

### Tax-Inclusive Pricing

```php
<?php

$product = $client->products->create([
    'type' => 'service',
    'name_lang_map' => ['en' => 'Design Work'],
    'price_map' => [
        'EUR' => 119.00, // Price includes VAT
    ],
    'price_is_tax_inclusive' => true,
    'organization_tax_class_id' => 'org_tax_xxx',
]);
```

### Product with PEPPOL E-Invoicing Fields

For EU e-invoicing compliance, add PEPPOL classification:

```php
<?php

$product = $client->products->create([
    'type' => 'service',
    'name_lang_map' => ['en' => 'IT Consulting Services'],
    'price_map' => ['EUR' => 200.00],
    'organization_tax_class_id' => 'org_tax_xxx',

    // PEPPOL e-invoicing schema
    'invoice_schema_map' => [
        // CPV code (Common Procurement Vocabulary)
        'classification_identifier_cpv' => '72000000-5', // IT services

        // PEPPOL billing unit code
        'peppol_billing_unit_code' => 'HUR', // Hour
    ],

    'is_sold' => true,
]);
```

### Product with Stripe Integration

Link to Stripe products for payment processing:

```php
<?php

$product = $client->products->create([
    'type' => 'digital',
    'name_lang_map' => ['en' => 'Premium Plan'],
    'price_map' => ['EUR' => 99.00],
    'organization_tax_class_id' => 'org_tax_xxx',

    // Link to Stripe product IDs
    'stripe_product_id_list' => [
        'prod_xxx123', // Production product
        'prod_yyy456', // Test product
    ],

    'is_sold' => true,
]);
```

## Listing Products

### Basic List

```php
<?php

$products = $client->products->list();

foreach ($products as $product) {
    $name = $product->name_lang_map['en'] ?? array_values($product->name_lang_map)[0] ?? 'Unnamed';
    $price = $product->price_map[$product->primary_currency ?? array_keys($product->price_map)[0]] ?? 0;
    $currency = $product->primary_currency ?? array_keys($product->price_map)[0];

    echo "{$product->alias}: {$name} - {$price} {$currency}\n";
}
```

### Filtering Products

```php
<?php

// Filter by type
$services = $client->products->list([
    'filter' => [
        'type' => 'service',
    ],
]);

// Filter by sale status
$activeProducts = $client->products->list([
    'filter' => [
        'is_sold' => true,
    ],
]);

// Include related data
$products = $client->products->list([
    'include' => ['tax_class', 'tag_ids'],
]);
```

### Pagination

```php
<?php

$products = $client->products->list([
    'page' => 1,
    'per_page' => 25,
]);

echo "Total: {$products->getTotalCount()}\n";
echo "Page {$products->getCurrentPage()} of {$products->getTotalPages()}\n";
```

## Retrieving a Product

```php
<?php

$product = $client->products->retrieve('org_prod_xxx', [
    'include' => ['tax_class'],
]);

echo "Product: {$product->id}\n";
echo "Type: {$product->type}\n";
echo "Alias: {$product->alias}\n";

// Display prices
foreach ($product->price_map as $currency => $price) {
    $marker = ($currency === $product->primary_currency) ? ' (primary)' : '';
    echo "Price: {$price} {$currency}{$marker}\n";
}

// Display names in all languages
foreach ($product->name_lang_map as $lang => $name) {
    echo "Name [{$lang}]: {$name}\n";
}
```

## Updating a Product

```php
<?php

$product = $client->products->update('org_prod_xxx', [
    'price_map' => [
        'EUR' => 175.00, // Updated price
        'USD' => 190.00,
    ],
    'name_lang_map' => [
        'en' => 'Senior Consulting Hour',
        'ro' => 'Ora Consultanta Senior',
    ],
]);

echo "Updated product: {$product->id}\n";
```

## Deleting a Product

```php
<?php

// Soft delete
$product = $client->products->delete('org_prod_xxx');

echo "Deleted at: {$product->deleted_at}\n";
```

## Restoring a Deleted Product

```php
<?php

$product = $client->products->restore('org_prod_xxx');

echo "Restored product: {$product->id}\n";
```

## Tagging Products

```php
<?php

// Add tags
$product = $client->products->tag('org_prod_xxx', [
    'tags' => ['featured', 'bestseller'],
]);

// Remove tags
$product = $client->products->untag('org_prod_xxx', [
    'tags' => ['featured'],
]);
```

## Using Products in Invoices

When creating invoices, you can reference products:

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
            // Reference a product
            'organization_product_id' => 'org_prod_xxx',
            'quantity' => 5,
            // Price, tax, name inherited from product
        ],
        [
            // Inline item without product
            'name_lang_map' => ['en' => 'Custom Work'],
            'quantity' => 1,
            'price' => 500.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
        ],
    ],
]);
```

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `type` | string | Product type: `service`, `digital`, `physical`, `bonus` |
| `price_map` | object | Currency-price mapping (e.g., `{"EUR": 100.00}`) |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `alias` | string | Unique identifier (alphanumeric, dashes) |
| `name_lang_map` | object | Name by language (e.g., `{"en": "Name", "ro": "Nume"}`) |
| `description_lang_map` | object | Description by language |
| `unit_lang_map` | object | Unit by language (e.g., `{"en": "hour"}`) |
| `description` | string | Plain text description |
| `organization_tax_class_id` | string | Tax class ID |
| `price_is_tax_inclusive` | boolean | Whether prices include tax (default: false) |
| `primary_currency` | string | Primary currency when multiple prices exist |
| `invoice_schema_map` | object | PEPPOL e-invoicing fields |
| `stripe_product_id_list` | array | Linked Stripe product IDs |
| `ean_number` | string | EAN barcode number |
| `upc_number` | string | UPC barcode number |
| `is_sold` | boolean | Whether product is available for sale |

### PEPPOL Schema Fields

| Field | Description |
|-------|-------------|
| `classification_identifier_cpv` | EU Common Procurement Vocabulary code |
| `peppol_billing_unit_code` | PEPPOL BIS Billing unit code (HUR, DAY, MON, etc.) |

### Include Options

| Include | Description |
|---------|-------------|
| `tax_class` | Tax class details |
| `organization` | Organization details |
| `tag_ids` | Associated tag IDs |
| `deleted_by_user` | User who deleted (if soft-deleted) |

## Complete Example: Product Catalog Setup

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create tax class first (see taxes.md)
    $taxClass = $client->taxClasses->list(['filter' => ['type' => 'standard']])->data[0];

    // 2. Create service product
    $consulting = $client->products->create([
        'type' => 'service',
        'alias' => 'consulting-hour',
        'name_lang_map' => [
            'en' => 'Consulting Hour',
            'ro' => 'Ora Consultanta',
        ],
        'description_lang_map' => [
            'en' => 'Professional consulting services',
        ],
        'unit_lang_map' => [
            'en' => 'hour',
            'ro' => 'ora',
        ],
        'price_map' => [
            'EUR' => 150.00,
            'RON' => 750.00,
        ],
        'primary_currency' => 'EUR',
        'organization_tax_class_id' => $taxClass->id,
        'invoice_schema_map' => [
            'peppol_billing_unit_code' => 'HUR',
        ],
        'is_sold' => true,
    ]);

    // 3. Create digital product
    $license = $client->products->create([
        'type' => 'digital',
        'alias' => 'software-license',
        'name_lang_map' => ['en' => 'Software License (Annual)'],
        'price_map' => ['EUR' => 299.00],
        'organization_tax_class_id' => $taxClass->id,
        'is_sold' => true,
    ]);

    // 4. Tag products
    $client->products->tag($consulting->id, [
        'tags' => ['featured', 'services'],
    ]);

    echo "Product catalog created successfully!\n";
    echo "Consulting: {$consulting->id}\n";
    echo "License: {$license->id}\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Invoices](invoices.md) - Use products in invoices
- [Taxes](taxes.md) - Configure tax classes for products
- [Proposals](proposals.md) - Use products in proposals
