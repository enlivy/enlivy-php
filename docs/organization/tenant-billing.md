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

### Retrying a failed charge

When collection on a subscription invoice fails, Enlivy retries on a fixed ladder before giving up. You can also trigger an attempt yourself — after fixing the card, for instance, rather than waiting for the next scheduled retry:

```php
$invoice = $client->tenantBillingInvoices->charge('org_inv_xxx');

$result = $invoice->lastResponse()?->json['meta']['charge_result'] ?? null;
```

The returned record is the refreshed invoice; the outcome of the attempt itself is on the response meta as `charge_result`. An API-charged invoice also carries the retry state directly:

| Field | Description |
|-------|-------------|
| `charge_first_failed_at` | When collection first failed |
| `charge_retry_count` | Attempts made so far |
| `next_charge_retry_at` | When the next automatic attempt is due |
| `charge_retry_exhausted_at` | When the ladder ran out; `null` while retries remain |

These four appear only on invoices Enlivy charges. The total number of attempts on the ladder is published as `tenant_billing.charge_retry_attempts_total` on the frontend bootstrap payload.

## Who the subscription is billed to

By default the Enlivy subscription is billed to the operating organization. You can override that — billing a parent entity, for example — without changing the organization itself:

```php
$identity = $client->tenantBilling->billingIdentity();

if (! $identity->is_custom) {
    echo "Billed to the operating organization\n";
}

// The identity actually in force, override or not
print_r($identity->effective);
```

```php
$client->tenantBilling->updateBillingIdentity([
    'custom_identity_name'             => 'Parent Holding SRL',
    'custom_identity_country_code'     => 'RO',
    'custom_identity_address_line_1'   => 'Str. Exemplu 1',
    'custom_identity_address_city'     => 'Cluj-Napoca',
    'custom_identity_address_iso_3166' => 'RO-CJ',
    'custom_identity_address_zip_code' => '400001',
    'custom_identity_email_address'    => 'billing@parent.example',
]);
```

Sending `custom_identity_name => null` clears the override and returns billing to the operating organization. Whenever a name is present, the country code, address line, city, subdivision and zip code must come with it — a half-stated identity is rejected rather than stored.

`effective` always resolves to whoever is actually billed, so read it rather than branching on `is_custom` yourself.

## Related

- [Invoices](invoices.md) — invoice charging and charge logs
- [Users](users.md) — organization-user payment methods and bank accounts
