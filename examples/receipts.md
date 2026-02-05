# Receipts

Create and manage receipts for payments received.

## Creating a Receipt

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$receipt = $client->receipts->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'currency' => 'EUR',
    'amount' => 500.00,
    'payment_method' => 'cash',
    'issued_at' => '2026-02-05',
    'description' => 'Payment for Invoice #INV-2026-0001',
]);

echo "Receipt created: {$receipt->number}\n";
```

## Receipt with Prefix

```php
<?php

$receipt = $client->receipts->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_receipt_prefix_id' => 'org_rcpt_prefix_xxx',
    'currency' => 'EUR',
    'amount' => 1500.00,
    'payment_method' => 'bank_transfer',
    'issued_at' => '2026-02-05',
]);

echo "Receipt: {$receipt->number}\n"; // e.g., "RCP-2026-0001"
```

## Listing Receipts

```php
<?php

$receipts = $client->receipts->list([
    'filter' => [
        'organization_receiver_user_id' => 'org_user_xxx',
    ],
    'include' => ['receiver_user'],
]);

foreach ($receipts as $receipt) {
    echo "{$receipt->number}: {$receipt->amount} {$receipt->currency}\n";
}
```

## Receipt Prefixes

```php
<?php

// Create prefix
$prefix = $client->receiptPrefixes->create([
    'name' => 'Standard Receipts',
    'prefix' => 'RCP',
    'next_number' => 1,
    'padding' => 4,
]);

// List prefixes
$prefixes = $client->receiptPrefixes->list();
```

## Download Receipt PDF

```php
<?php

$pdf = $client->receipts->download('org_rcpt_xxx');
file_put_contents('receipt.pdf', $pdf);
```

## Related

- [Invoices](invoices.md) - Create invoices before receipts
- [Organization Users](organization-users.md) - Receipt recipients
