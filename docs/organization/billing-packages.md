# Billing Packages

Billing packages are reusable templates for creating proposals. They define payment plans, product groups, and optional contract templates that can be offered to prospects and customers.

## Key Concepts

### Structure

```
BillingPackage
    |
    +-- Groups (product groupings)
    |       |
    |       +-- Items (products/services)
    |
    +-- Payment Plans (pricing options)
    |
    +-- Contract Templates (optional contracts)
            |
            +-- Sections (contract content)
```

### Types

Billing packages have a `type` field that categorizes the kind of offering.

## Creating a Billing Package

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$package = $client->billingPackages->create([
    'alias' => 'web-design-2026',
    'name_lang_map' => [
        'en' => 'Web Design Package',
        'ro' => 'Pachet Design Web',
    ],
    'description_lang_map' => [
        'en' => 'Complete website design and development package.',
    ],
    'locale' => 'en',
    'locale_list' => ['en', 'ro'],
    'type' => 'standard',
    'is_active' => true,

    // Optional: link to a project
    'organization_project_id' => 'org_proj_xxx',

    // Optional: expiration
    'expires_at' => '2026-12-31',

    // Proposal validity (seconds)
    'proposal_valid_for_seconds' => 604800, // 7 days

    // Allowed payment methods
    'allowed_payment_methods' => ['bank_transfer', 'card'],

    // Product groups
    'groups' => [
        [
            'name_lang_map' => ['en' => 'Design Services'],
            'order' => 1,
            'items' => [
                [
                    'organization_product_id' => 'org_prod_xxx',
                    'quantity' => 1,
                    'order' => 1,
                ],
            ],
        ],
    ],

    // Payment plans
    'payment_plans' => [
        [
            'name_lang_map' => ['en' => 'Monthly Plan'],
            'description_lang_map' => ['en' => 'Pay in 3 monthly installments'],
            'currency' => 'EUR',
            'frequency' => 'monthly',
            'due_date_type' => 'days',
            'due_date_days' => 30,
            'is_active' => true,
            'order' => 1,
        ],
        [
            'name_lang_map' => ['en' => 'One-Time Payment'],
            'description_lang_map' => ['en' => 'Pay upfront with 10% discount'],
            'currency' => 'EUR',
            'frequency' => 'one_time',
            'due_date_type' => 'days',
            'due_date_days' => 0,
            'is_active' => true,
            'order' => 2,
        ],
    ],
]);

echo "Billing package created: {$package->id}\n";
```

## Listing Billing Packages

```php
<?php

// List all active packages
$packages = $client->billingPackages->list([
    'is_active' => true,
    'include' => ['groups', 'payment_plans'],
]);

foreach ($packages as $package) {
    echo "{$package->alias}: " . ($package->name_lang_map['en'] ?? 'Untitled') . "\n";
}

// Filter by project
$projectPackages = $client->billingPackages->list([
    'organization_project_id' => 'org_proj_xxx',
]);

// Only available (active + not expired)
$available = $client->billingPackages->list([
    'only_available' => true,
]);
```

## Retrieving a Billing Package

```php
<?php

$package = $client->billingPackages->retrieve('org_bp_xxx', [
    'include' => ['groups', 'payment_plans', 'contract_templates', 'created_by_user'],
]);

echo "Package: " . ($package->name_lang_map['en'] ?? '') . "\n";
echo "Active: " . ($package->is_active ? 'Yes' : 'No') . "\n";
echo "Available: " . ($package->is_available ? 'Yes' : 'No') . "\n";
```

## Updating a Billing Package

```php
<?php

$package = $client->billingPackages->update('org_bp_xxx', [
    'name_lang_map' => [
        'en' => 'Web Design Package (Updated)',
    ],
    'is_active' => true,
]);
```

## Expiring a Billing Package

Expire a billing package to make it unavailable for new proposals without deleting it:

```php
<?php

$package = $client->billingPackages->expire('org_bp_xxx');

echo "Expired at: {$package->expired_at}\n";
```

## Deleting and Restoring

```php
<?php

// Soft-delete
$package = $client->billingPackages->delete('org_bp_xxx');

// Restore
$package = $client->billingPackages->restore('org_bp_xxx');
```

## Creating a Proposal from a Billing Package

Use the proposals service to create a proposal directly from a billing package:

```php
<?php

$proposal = $client->proposals->fromBillingPackage([
    'organization_billing_package_id' => 'org_bp_xxx',
    'organization_billing_package_payment_plan_id' => 'org_bp_plan_xxx',

    // Recipient (choose one)
    'organization_prospect_id' => 'org_pros_xxx',
    // OR: 'organization_receiver_user_id' => 'org_user_xxx',
    // OR: 'recipient_email' => 'client@example.com', 'recipient_name' => 'John Doe',

    // Optional sender
    'organization_sender_user_id' => 'org_user_xxx',
]);

echo "Proposal created: {$proposal->id}\n";
```

## Client Portal: Billing Packages

Customers can browse and claim billing packages through the Client Portal:

```php
<?php

use Enlivy\EnlivyPortalClient;

$portal = new EnlivyPortalClient([
    'portal_token' => 'session-uuid-token',
    'organization_id' => 'org_xxx',
]);

// List available packages
$packages = $portal->billingPackages->list();

// View package details
$package = $portal->billingPackages->retrieve('org_bp_xxx');

// Claim a package (creates an accepted proposal)
$result = $portal->billingPackages->claim('org_bp_xxx', [
    'organization_billing_package_payment_plan_id' => 'org_bp_plan_xxx',
]);
```

## Available Includes

| Include | Description |
|---------|-------------|
| `organization` | Parent organization |
| `project` | Linked project |
| `groups` | Product groups with items |
| `payment_plans` | Available payment plans |
| `contract_templates` | Attached contract templates |
| `created_by_user` | User who created the package |
| `deleted_by_user` | User who deleted the package |
| `expired_by_user` | User who expired the package |

## Available Filters

| Filter | Type | Description |
|--------|------|-------------|
| `is_active` | bool | Filter by active status |
| `type` | string | Filter by package type |
| `organization_project_id` | string | Filter by project |
| `only_available` | bool | Only active and non-expired |

## Related

- [Proposals](proposals.md) - Create proposals from billing packages
- [Products](products.md) - Products used in package groups
- [Contracts](contracts.md) - Contract templates in packages
