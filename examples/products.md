# Products

Manage your product and service catalog.

## Creating Products

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// Service product
$service = $client->products->create([
    'name_lang_map' => [
        'en' => 'Consulting Hour',
        'ro' => 'Oră Consultanță',
    ],
    'description_lang_map' => [
        'en' => 'One hour of professional consulting',
    ],
    'type' => 'service',
    'price' => 150.00,
    'currency' => 'EUR',
    'organization_tax_class_id' => 'org_tax_xxx',
    'sku' => 'CONSULT-HOUR',
    'is_active' => true,
]);

// Physical product
$physical = $client->products->create([
    'name_lang_map' => ['en' => 'Laptop Stand'],
    'type' => 'physical',
    'price' => 49.99,
    'currency' => 'EUR',
    'organization_tax_class_id' => 'org_tax_xxx',
    'sku' => 'STAND-001',
    'weight' => 1.5,
    'weight_unit' => 'kg',
]);

// Digital product
$digital = $client->products->create([
    'name_lang_map' => ['en' => 'Software License (Annual)'],
    'type' => 'digital',
    'price' => 299.00,
    'currency' => 'EUR',
    'organization_tax_class_id' => 'org_tax_xxx',
    'sku' => 'LICENSE-ANNUAL',
]);
```

## Listing Products

```php
<?php

$products = $client->products->list([
    'filter' => [
        'is_active' => true,
        'type' => 'service',
    ],
    'include' => ['tax_class'],
]);

foreach ($products as $product) {
    echo "{$product->sku}: {$product->name} - {$product->price} {$product->currency}\n";
}
```

## Using Products in Invoices

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
            'organization_product_id' => 'org_prod_xxx', // Use product
            'quantity' => 5,
            // Price and tax inherited from product
        ],
        [
            // Or inline item without product
            'name_lang_map' => ['en' => 'Custom Work'],
            'quantity' => 1,
            'price' => 500.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
        ],
    ],
]);
```

## Product Types

| Type | Description |
|------|-------------|
| `service` | Services, consulting, labor |
| `digital` | Software, licenses, downloads |
| `physical` | Physical goods requiring shipping |
| `bonus` | Complimentary items, discounts |

## Related

- [Invoices](invoices.md) - Use products in invoices
- [Taxes](taxes.md) - Configure tax classes for products
