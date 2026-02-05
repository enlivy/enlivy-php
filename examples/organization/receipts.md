# Receipts

Create and manage receipts for payments received or made. Receipts can be linked to invoices and contracts.

## Key Concepts

### Direction

| Direction | Description |
|-----------|-------------|
| `outbound` | Payment received (you receive money) |
| `inbound` | Payment made (you pay money) |

### Status

Receipts use the same status values as invoices:

| Status | Description |
|--------|-------------|
| `draft` | Not yet finalized |
| `pending` | Awaiting payment (default) |
| `paid` | Payment completed |
| `canceled` | Receipt canceled |
| `scheduled` | Scheduled for future |

## Creating Receipts

### Basic Receipt

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$receipt = $client->receipts->create([
    // Required fields
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'currency' => 'EUR',
    'sub_total' => 420.17,
    'tax_total' => 79.83,
    'total' => 500.00,
    'is_tax_charged' => true,

    // Receipt number (required if no prefix)
    'receipt_number' => 'RCP-2026-0001',

    // Optional
    'direction' => 'outbound', // outbound (receiving) or inbound (paying)
    'issued_at' => '2026-02-05',
]);

echo "Receipt created: {$receipt->receipt_number}\n";
```

### Receipt with Auto-Numbering

Use a prefix for automatic receipt number generation:

```php
<?php

$receipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_receipt_prefix_id' => 'org_rcpt_prefix_xxx', // Auto-generates number
    'currency' => 'EUR',
    'sub_total' => 1260.50,
    'tax_total' => 239.50,
    'total' => 1500.00,
    'is_tax_charged' => true,
    'issued_at' => '2026-02-05',
]);

echo "Receipt: {$receipt->receipt_number}\n"; // e.g., "RCP-2026-0001"
```

### Receipt Linked to Invoice

```php
<?php

$receipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_invoice_id' => 'org_inv_xxx', // Link to invoice
    'receipt_number' => 'RCP-2026-0002',
    'currency' => 'EUR',
    'sub_total' => 1000.00,
    'tax_total' => 190.00,
    'total' => 1190.00,
    'is_tax_charged' => true,
    'direction' => 'outbound',
    'issued_at' => '2026-02-05',
    'paid_at' => '2026-02-05', // Mark as paid
    'status' => 'paid',
]);
```

### Receipt Linked to Contract

```php
<?php

$receipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_contract_id' => 'org_cont_xxx', // Link to contract
    'receipt_number' => 'RCP-2026-0003',
    'currency' => 'EUR',
    'sub_total' => 5000.00,
    'tax_total' => 950.00,
    'total' => 5950.00,
    'is_tax_charged' => true,
    'direction' => 'outbound',
    'issued_at' => '2026-02-05',
]);
```

### Receipt with Tax Details

Include detailed tax breakdown:

```php
<?php

$receipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'receipt_number' => 'RCP-2026-0004',
    'currency' => 'EUR',
    'sub_total' => 1000.00,
    'tax_total' => 190.00,
    'total' => 1190.00,
    'is_tax_charged' => true,
    'direction' => 'outbound',
    'issued_at' => '2026-02-05',

    // Tax breakdown
    'taxes' => [
        [
            'organization_tax_class_id' => 'org_tax_class_xxx',
            'organization_tax_rate_id' => 'org_tax_rate_xxx',
            'amount' => 190.00,
        ],
    ],
]);
```

### Receipt with Sender (B2B)

For receipts between two organization users:

```php
<?php

$receipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_sender_user_id' => 'org_user_sender_xxx',    // Who sends
    'organization_receiver_user_id' => 'org_user_receiver_xxx', // Who receives
    'receipt_number' => 'RCP-2026-0005',
    'currency' => 'EUR',
    'sub_total' => 2500.00,
    'tax_total' => 0.00,
    'total' => 2500.00,
    'is_tax_charged' => false,
    'direction' => 'inbound', // You're paying
    'issued_at' => '2026-02-05',
]);
```

### Receipt with Discount

```php
<?php

$receipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'receipt_number' => 'RCP-2026-0006',
    'currency' => 'EUR',
    'sub_total' => 1000.00,
    'discount' => 100.00, // Discount applied
    'tax_total' => 171.00, // Tax on (1000 - 100) = 900 * 19%
    'total' => 971.00,
    'is_tax_charged' => true,
    'issued_at' => '2026-02-05',
]);
```

### Reverse Receipt (Credit Note)

```php
<?php

$reverseReceipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'receipt_number' => 'RCP-2026-0007',
    'currency' => 'EUR',
    'sub_total' => -500.00,
    'tax_total' => -95.00,
    'total' => -595.00,
    'is_tax_charged' => true,
    'is_reverse' => true, // Mark as reverse/credit
    'direction' => 'outbound',
    'issued_at' => '2026-02-05',
]);
```

## Listing Receipts

### Basic List

```php
<?php

$receipts = $client->receipts->list();

foreach ($receipts as $receipt) {
    echo "{$receipt->receipt_number}: {$receipt->total} {$receipt->currency}\n";
}
```

### With Filters

```php
<?php

// Filter by receiver
$receipts = $client->receipts->list([
    'filter' => [
        'organization_receiver_user_id' => 'org_user_xxx',
    ],
]);

// Filter by status
$paidReceipts = $client->receipts->list([
    'filter' => [
        'status' => 'paid',
    ],
]);

// Filter by direction
$inboundReceipts = $client->receipts->list([
    'filter' => [
        'direction' => 'inbound',
    ],
]);
```

### With Related Data

```php
<?php

$receipts = $client->receipts->list([
    'include' => [
        'receiver_user',
        'sender_user',
        'bank_account',
        'invoice',
        'taxes',
        'receipt_prefix',
    ],
]);

foreach ($receipts as $receipt) {
    echo "{$receipt->receipt_number}\n";
    echo "  From: {$receipt->sender_user->name}\n";
    echo "  To: {$receipt->receiver_user->name}\n";
    echo "  Total: {$receipt->total} {$receipt->currency}\n";
}
```

## Retrieving a Receipt

```php
<?php

$receipt = $client->receipts->retrieve('org_rcpt_xxx', [
    'include' => ['receiver_user', 'bank_account', 'taxes'],
]);

echo "Receipt: {$receipt->receipt_number}\n";
echo "Status: {$receipt->status}\n";
echo "Direction: {$receipt->direction}\n";
echo "Sub Total: {$receipt->sub_total}\n";
echo "Tax Total: {$receipt->tax_total}\n";
echo "Total: {$receipt->total} {$receipt->currency}\n";
echo "Tax Charged: " . ($receipt->is_tax_charged ? 'Yes' : 'No') . "\n";

if ($receipt->issued_at) {
    echo "Issued: {$receipt->issued_at}\n";
}

if ($receipt->paid_at) {
    echo "Paid: {$receipt->paid_at}\n";
}

// Show taxes
if (!empty($receipt->taxes)) {
    echo "Taxes:\n";
    foreach ($receipt->taxes as $tax) {
        echo "  - {$tax->amount} ({$tax->organization_tax_class_id})\n";
    }
}
```

## Updating a Receipt

```php
<?php

$receipt = $client->receipts->update('org_rcpt_xxx', [
    'status' => 'paid',
    'paid_at' => '2026-02-05',
]);

echo "Receipt updated: {$receipt->receipt_number}\n";
echo "Status: {$receipt->status}\n";
```

## Deleting a Receipt

```php
<?php

// Soft delete
$receipt = $client->receipts->delete('org_rcpt_xxx');

echo "Deleted at: {$receipt->deleted_at}\n";
```

## Restoring a Receipt

```php
<?php

$receipt = $client->receipts->restore('org_rcpt_xxx');

echo "Restored: {$receipt->receipt_number}\n";
```

## Tagging Receipts

```php
<?php

// Add tags
$receipt = $client->receipts->tag('org_rcpt_xxx', [
    'tags' => ['q1-2026', 'verified'],
]);

// Remove tags
$receipt = $client->receipts->untag('org_rcpt_xxx', [
    'tags' => ['verified'],
]);
```

## Receipt Prefixes

### Create Prefix

```php
<?php

$prefix = $client->receiptPrefixes->create([
    'name' => 'Standard Receipts',
    'prefix' => 'RCP',
    'next_number' => 1,
    'padding' => 4, // Results in RCP-0001, RCP-0002, etc.
]);

echo "Created prefix: {$prefix->id}\n";
```

### List Prefixes

```php
<?php

$prefixes = $client->receiptPrefixes->list();

foreach ($prefixes as $prefix) {
    echo "{$prefix->prefix}: Next #{$prefix->next_number}\n";
}
```

## Upload Receipt File

Attach a file (PDF, image) to a receipt:

```php
<?php

// When creating
$receipt = $client->receipts->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'organization_receiver_user_id' => 'org_user_xxx',
    'receipt_number' => 'RCP-2026-0008',
    'currency' => 'EUR',
    'sub_total' => 100.00,
    'tax_total' => 19.00,
    'total' => 119.00,
    'is_tax_charged' => true,
    'file' => fopen('/path/to/receipt.pdf', 'r'),
]);
```

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `organization_bank_account_id` | string | Bank account ID |
| `organization_receiver_user_id` | string | Receiver user ID |
| `currency` | string | ISO 4217 currency code |
| `sub_total` | numeric | Subtotal before tax |
| `total` | numeric | Total amount |
| `is_tax_charged` | boolean | Whether tax is included |
| `receipt_number` | string | Receipt number (required without prefix) |
| `organization_receipt_prefix_id` | string | Prefix ID (required without receipt_number) |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `organization_sender_user_id` | string | Sender user ID |
| `organization_invoice_id` | string | Linked invoice ID |
| `organization_contract_id` | string | Linked contract ID |
| `tax_total` | numeric | Tax amount |
| `discount` | numeric | Discount amount |
| `status` | string | Receipt status |
| `direction` | string | `inbound` or `outbound` |
| `is_reverse` | boolean | Whether this is a reverse/credit receipt |
| `issued_at` | datetime | Issue date |
| `due_at` | datetime | Due date |
| `paid_at` | datetime | Payment date |
| `taxes` | array | Tax breakdown |
| `file` | file | Receipt file (PDF, image) |

### Tax Object Fields

| Field | Type | Description |
|-------|------|-------------|
| `organization_tax_class_id` | string | Tax class ID (required) |
| `organization_tax_rate_id` | string | Tax rate ID |
| `amount` | numeric | Tax amount |

### Include Options

| Include | Description |
|---------|-------------|
| `organization` | Organization details |
| `invoice` | Linked invoice |
| `bank_account` | Bank account details |
| `sender_user` | Sender user details |
| `receiver_user` | Receiver user details |
| `taxes` | Tax breakdown |
| `receipt_prefix` | Receipt prefix details |
| `tag_ids` | Associated tag IDs |
| `deleted_by_user` | User who deleted |

## Complete Example: Payment Recording

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create receipt prefix
    $prefix = $client->receiptPrefixes->create([
        'name' => 'Payment Receipts 2026',
        'prefix' => 'PAY',
        'next_number' => 1,
        'padding' => 5,
    ]);

    // 2. Get bank account
    $bankAccounts = $client->bankAccounts->list([
        'filter' => ['currency' => 'EUR'],
    ]);
    $bankAccount = $bankAccounts->data[0];

    // 3. Get customer
    $customers = $client->organizationUsers->list([
        'filter' => ['role.can_be_invoiced' => true],
    ]);
    $customer = $customers->data[0];

    // 4. Create receipt for payment received
    $receipt = $client->receipts->create([
        'organization_bank_account_id' => $bankAccount->id,
        'organization_receiver_user_id' => $customer->id,
        'organization_receipt_prefix_id' => $prefix->id,
        'currency' => 'EUR',
        'sub_total' => 840.34,
        'tax_total' => 159.66,
        'total' => 1000.00,
        'is_tax_charged' => true,
        'direction' => 'outbound',
        'status' => 'paid',
        'issued_at' => date('Y-m-d H:i:s'),
        'paid_at' => date('Y-m-d H:i:s'),
    ]);

    // 5. Tag receipt
    $client->receipts->tag($receipt->id, [
        'tags' => ['payment', '2026'],
    ]);

    echo "Receipt created: {$receipt->receipt_number}\n";
    echo "Total: {$receipt->total} {$receipt->currency}\n";
    echo "Status: {$receipt->status}\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Invoices](invoices.md) - Create invoices before receipts
- [Bank Accounts](bank-accounts.md) - Bank account for receipts
- [Organization Users](organization-users.md) - Receipt recipients
- [Contracts](contracts.md) - Link receipts to contracts
