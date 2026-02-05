# Bank Accounts

Manage organization bank accounts for invoicing and payment tracking.

## Key Concepts

### Account Types

| Type | Description |
|------|-------------|
| `standard` | Traditional bank account with IBAN/SWIFT |
| `paypal` | PayPal account |
| `stripe` | Stripe connected account |

### Account Information

Bank account details are stored in the `account_information` object. The structure depends on the account type.

## Creating Bank Accounts

### Standard Bank Account

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$bankAccount = $client->bankAccounts->create([
    // Required fields
    'name' => 'Main Business Account',
    'currency' => 'EUR',
    'bank_name' => 'Banca Transilvania',
    'bank_address' => 'Str. George Baritiu 8, Cluj-Napoca, Romania',
    'bank_country_code' => 'RO',

    // Account details (required)
    'account_information' => [
        'iban' => 'RO49BTRLEURCRT0123456789',
        'swift' => 'BTRLRO22',
    ],

    // Optional
    'type' => 'standard', // standard (default), paypal, stripe
    'balance' => 0.00,
]);

echo "Bank account created: {$bankAccount->id}\n";
```

### Bank Account with Payment QR Codes

Enable QR code generation for invoices:

```php
<?php

$bankAccount = $client->bankAccounts->create([
    'name' => 'EUR Business Account',
    'currency' => 'EUR',
    'bank_name' => 'ING Bank',
    'bank_address' => 'Str. Ion Mihalache 1, Bucharest, Romania',
    'bank_country_code' => 'RO',
    'account_information' => [
        'iban' => 'RO49INGBEURCRT0123456789',
        'swift' => 'INGBROBU',
    ],

    // Enable QR code types
    'payment_qr_types' => [
        'sepa',       // SEPA payment QR
        'iso_20022',  // ISO 20022 standard
        'ropay',      // Romanian RoPay QR
    ],
]);
```

### PayPal Account

```php
<?php

$paypalAccount = $client->bankAccounts->create([
    'name' => 'PayPal Business',
    'currency' => 'USD',
    'bank_name' => 'PayPal',
    'bank_address' => '2211 N First Street, San Jose, CA 95131, USA',
    'bank_country_code' => 'US',
    'type' => 'paypal',
    'account_information' => [
        'email' => 'business@company.com',
    ],
]);
```

### Stripe Account

```php
<?php

$stripeAccount = $client->bankAccounts->create([
    'name' => 'Stripe Payments',
    'currency' => 'EUR',
    'bank_name' => 'Stripe',
    'bank_address' => '354 Oyster Point Blvd, South San Francisco, CA 94080, USA',
    'bank_country_code' => 'US',
    'type' => 'stripe',
    'account_information' => [
        'account_id' => 'acct_xxx123',
    ],
]);
```

## Listing Bank Accounts

### Basic List

```php
<?php

$accounts = $client->bankAccounts->list();

foreach ($accounts as $account) {
    echo "{$account->name} ({$account->currency})\n";
    echo "  Bank: {$account->bank_name}\n";
    echo "  Type: {$account->type}\n";

    if ($account->type === 'standard') {
        echo "  IBAN: {$account->account_information['iban']}\n";
    }
}
```

### With Filters

```php
<?php

// Filter by currency
$eurAccounts = $client->bankAccounts->list([
    'filter' => [
        'currency' => 'EUR',
    ],
]);

// Filter by type
$standardAccounts = $client->bankAccounts->list([
    'filter' => [
        'type' => 'standard',
    ],
]);
```

### With Related Data

```php
<?php

$accounts = $client->bankAccounts->list([
    'include' => ['organization', 'tag_ids'],
]);

foreach ($accounts as $account) {
    echo "{$account->name}\n";
    // Access related data
}
```

## Retrieving a Bank Account

```php
<?php

$account = $client->bankAccounts->retrieve('org_bank_xxx');

echo "Name: {$account->name}\n";
echo "Currency: {$account->currency}\n";
echo "Bank: {$account->bank_name}\n";
echo "Bank Country: {$account->bank_country_code}\n";
echo "Balance: {$account->balance}\n";

// Access account details based on type
switch ($account->type) {
    case 'standard':
        echo "IBAN: {$account->account_information['iban']}\n";
        echo "SWIFT: {$account->account_information['swift']}\n";
        break;
    case 'paypal':
        echo "Email: {$account->account_information['email']}\n";
        break;
    case 'stripe':
        echo "Account ID: {$account->account_information['account_id']}\n";
        break;
}

// Check QR types
if (!empty($account->payment_qr_types)) {
    echo "QR Types: " . implode(', ', $account->payment_qr_types) . "\n";
}
```

## Updating a Bank Account

```php
<?php

$account = $client->bankAccounts->update('org_bank_xxx', [
    'name' => 'Primary EUR Account',
    'balance' => 15000.00,
    'payment_qr_types' => ['sepa', 'iso_20022'],
]);

echo "Updated: {$account->name}\n";
```

## Deleting a Bank Account

```php
<?php

// Soft delete
$account = $client->bankAccounts->delete('org_bank_xxx');

echo "Deleted at: {$account->deleted_at}\n";
```

## Restoring a Deleted Bank Account

```php
<?php

$account = $client->bankAccounts->restore('org_bank_xxx');

echo "Restored: {$account->name}\n";
```

## Tagging Bank Accounts

```php
<?php

// Add tags
$account = $client->bankAccounts->tag('org_bank_xxx', [
    'tags' => ['primary', 'operational'],
]);

// Remove tags
$account = $client->bankAccounts->untag('org_bank_xxx', [
    'tags' => ['operational'],
]);
```

## Using Bank Accounts in Invoices

When creating invoices, specify the bank account for payment details:

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_bank_account_id' => 'org_bank_xxx', // Payment details on invoice
    'status' => 'draft',
    'currency' => 'EUR',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',
    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Consulting Services'],
            'quantity' => 10,
            'price' => 100.00,
            'type' => 'service',
        ],
    ],
]);

// The invoice PDF will include bank account details
// If payment_qr_types are enabled, QR codes will be generated
```

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Unique account name (max 255 chars) |
| `currency` | string | ISO 4217 currency code |
| `bank_name` | string | Bank name (max 255 chars) |
| `bank_address` | string | Bank address (max 255 chars) |
| `bank_country_code` | string | Bank country (ISO 3166-1 alpha-2) |
| `account_information` | object | Account details (structure depends on type) |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `type` | string | Account type: `standard`, `paypal`, `stripe` (default: standard) |
| `balance` | numeric | Current balance |
| `payment_qr_types` | array | QR code types: `iso_20022`, `ropay`, `sepa` |

### Account Information by Type

**Standard Account:**
```php
'account_information' => [
    'iban' => 'RO49BTRLEURCRT0123456789',
    'swift' => 'BTRLRO22',
]
```

**PayPal Account:**
```php
'account_information' => [
    'email' => 'business@company.com',
]
```

**Stripe Account:**
```php
'account_information' => [
    'account_id' => 'acct_xxx123',
]
```

### Payment QR Types

| Type | Description |
|------|-------------|
| `iso_20022` | ISO 20022 standard QR code |
| `ropay` | Romanian RoPay payment QR |
| `sepa` | SEPA payment QR code |

### Include Options

| Include | Description |
|---------|-------------|
| `organization` | Organization details |
| `tag_ids` | Associated tag IDs |
| `deleted_by_user` | User who deleted (if soft-deleted) |

## Complete Example: Bank Account Setup

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create main EUR account
    $eurAccount = $client->bankAccounts->create([
        'name' => 'Main EUR Account',
        'currency' => 'EUR',
        'bank_name' => 'Banca Transilvania',
        'bank_address' => 'Str. George Baritiu 8, Cluj-Napoca, Romania',
        'bank_country_code' => 'RO',
        'type' => 'standard',
        'account_information' => [
            'iban' => 'RO49BTRLEURCRT0123456789',
            'swift' => 'BTRLRO22',
        ],
        'payment_qr_types' => ['sepa', 'ropay'],
    ]);

    // 2. Create RON account
    $ronAccount = $client->bankAccounts->create([
        'name' => 'RON Operations Account',
        'currency' => 'RON',
        'bank_name' => 'Banca Transilvania',
        'bank_address' => 'Str. George Baritiu 8, Cluj-Napoca, Romania',
        'bank_country_code' => 'RO',
        'type' => 'standard',
        'account_information' => [
            'iban' => 'RO49BTRLRONCRT0123456789',
            'swift' => 'BTRLRO22',
        ],
        'payment_qr_types' => ['ropay'],
    ]);

    // 3. Tag accounts
    $client->bankAccounts->tag($eurAccount->id, [
        'tags' => ['primary', 'invoicing'],
    ]);

    $client->bankAccounts->tag($ronAccount->id, [
        'tags' => ['operational'],
    ]);

    echo "Bank accounts created successfully!\n";
    echo "EUR Account: {$eurAccount->id}\n";
    echo "RON Account: {$ronAccount->id}\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Invoices](invoices.md) - Link bank accounts to invoices
- [Receipts](receipts.md) - Track payments to bank accounts
