# Bank Accounts

Manage bank accounts and track transactions.

## Creating a Bank Account

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$bankAccount = $client->bankAccounts->create([
    'name' => 'Main Business Account',
    'bank_name' => 'Banca Transilvania',
    'iban' => 'RO49AAAA1B31007593840000',
    'swift' => 'BTRLRO22',
    'currency' => 'EUR',
    'is_default' => true,
]);

echo "Bank account created: {$bankAccount->id}\n";
```

## Listing Bank Accounts

```php
<?php

$accounts = $client->bankAccounts->list();

foreach ($accounts as $account) {
    $default = $account->is_default ? ' (default)' : '';
    echo "{$account->name}: {$account->iban}{$default}\n";
}
```

## Bank Transactions

### Create Transaction

```php
<?php

$transaction = $client->bankTransactions->create([
    'organization_bank_account_id' => 'org_bank_xxx',
    'type' => 'credit', // credit or debit
    'amount' => 5000.00,
    'currency' => 'EUR',
    'description' => 'Payment received - Invoice #INV-2026-0001',
    'reference' => 'INV-2026-0001',
    'transaction_date' => '2026-02-05',
    'organization_bank_transaction_cost_type_id' => 'org_cost_type_xxx',
]);
```

### List Transactions

```php
<?php

$transactions = $client->bankTransactions->list([
    'filter' => [
        'organization_bank_account_id' => 'org_bank_xxx',
        'transaction_date_from' => '2026-01-01',
        'transaction_date_to' => '2026-01-31',
    ],
]);

$total = 0;
foreach ($transactions as $tx) {
    $sign = $tx->type === 'credit' ? '+' : '-';
    echo "{$tx->transaction_date}: {$sign}{$tx->amount} {$tx->currency} - {$tx->description}\n";
    $total += $tx->type === 'credit' ? $tx->amount : -$tx->amount;
}
echo "Net: {$total}\n";
```

## Transaction Cost Types

Categorize transactions for reporting.

```php
<?php

// Create cost type
$costType = $client->bankTransactionCostTypes->create([
    'name' => 'Client Payments',
    'code' => 'INCOME-CLIENT',
    'type' => 'income',
]);

// List cost types
$costTypes = $client->bankTransactionCostTypes->list();

foreach ($costTypes as $type) {
    echo "{$type->code}: {$type->name} ({$type->type})\n";
}
```

## Bank Account Data (Open Banking)

For connected bank accounts via Open Banking:

```php
<?php

// Get account data
$data = $client->bankAccountData->retrieve('org_bank_data_xxx');

echo "Balance: {$data->balance} {$data->currency}\n";
echo "Last synced: {$data->last_synced_at}\n";
```

## Using Bank Account in Invoices

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
    'line_items' => [/* ... */],
]);
```

## Related

- [Invoices](invoices.md) - Link bank accounts to invoices
