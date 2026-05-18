# Tenant Billing

Tenant billing is the system through which **Enlivy charges a customer organization** for its
Enlivy subscription — Feature Packs and Capacity Add-ons. This is distinct from the
organization's *own* invoicing of *its* customers.

## Key Concepts

| Resource | Purpose |
|----------|---------|
| `tenantBilling` | Catalog, current state, terms, usage, preview & apply changes |
| `tenantBillingTrial` | 30-day trial activation and mid-trial pack toggles |
| `tenantBillingPaymentMethods` | Cards used to pay the Enlivy subscription |
| `tenantBillingInvoices` | Read/download the Enlivy-issued subscription invoices |

## Reading State & Catalog

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// What can be purchased (packs, add-ons, prices, caps)
$catalog = $client->tenantBilling->catalog();

// Current subscription state (active packs/addons, trial, cycle)
$state = $client->tenantBilling->state([
    'include' => 'billing_schedule,credits,usage_stats',
]);

// Subscription terms / Enlivy tax structure
$terms = $client->tenantBilling->terms();

// Metered usage vs granted caps
$usage = $client->tenantBilling->usage();
```

## Previewing & Applying Changes

`preview` returns the price/proration impact without committing; `apply` commits it.

```php
$change = [
    'feature_packs' => ['growth'],
    'capacity_addons' => ['extra_seats' => 2],
    'billing_cycle' => 'yearly',
];

$preview = $client->tenantBilling->preview($change);
$result  = $client->tenantBilling->apply($change);
```

## Trial

```php
$client->tenantBillingTrial->activate();
$client->tenantBillingTrial->addPack(['pack' => 'growth']);
$client->tenantBillingTrial->dropPack('growth');
```

## Payment Methods (Enlivy subscription)

These belong to Enlivy SRL's mirror user, not the tenant organization's own users.

```php
// Begin collecting a new card off-session
$intent = $client->tenantBillingPaymentMethods->setupIntent();

$methods = $client->tenantBillingPaymentMethods->list();
$method  = $client->tenantBillingPaymentMethods->retrieve('org_user_pm_xxx');

$client->tenantBillingPaymentMethods->setAsDefault('org_user_pm_xxx');
$client->tenantBillingPaymentMethods->delete('org_user_pm_xxx');
$client->tenantBillingPaymentMethods->restore('org_user_pm_xxx');
```

Available includes: `organization_user`, `stripe_data`, `created_by_user`, `deleted_by_user`.

## Subscription Invoices

```php
$invoices = $client->tenantBillingInvoices->list();
$invoice  = $client->tenantBillingInvoices->retrieve('org_inv_xxx', [
    'include' => 'line_items,taxes,latest_charge_log',
]);

$pdf = $client->tenantBillingInvoices->download('org_inv_xxx');
file_put_contents('enlivy-subscription-invoice.pdf', $pdf);
```

## Related

- [Invoices](invoices.md) — invoice charging and charge logs
- [Users](users.md) — organization-user payment methods and bank accounts
