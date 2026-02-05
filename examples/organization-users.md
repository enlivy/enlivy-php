# Organization Users

Organization Users represent people and companies within your organization. They can be:
- **Internal users**: Employees, team members
- **External users**: Customers, clients, vendors

This is the entity you use when you need to invoice someone.

## Key Concepts

### OrganizationUser vs Prospect

| Entity | Purpose | Can Be Invoiced? |
|--------|---------|------------------|
| **OrganizationUser** | Actual person/company in your system | Yes (with proper role) |
| **Prospect** | Sales lead in your pipeline | No - must be linked first |

### User Roles

User roles determine what an organization user can do:

| Role Flag | Meaning |
|-----------|---------|
| `can_be_invoiced` | User can receive invoices (customer) |
| `can_be_invoicing` | User can send invoices (sender identity) |
| `is_business_entity` | User is a company (vs individual) |

## Creating a Customer

### Step 1: Get or Create a Customer Role

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// Find existing customer role
$roles = $client->userRoles->list([
    'filter' => [
        'can_be_invoiced' => true,
        'is_business_entity' => false, // for individuals
    ],
]);

$customerRole = $roles->data[0] ?? null;

// Or create a new role
if (!$customerRole) {
    $customerRole = $client->userRoles->create([
        'name' => 'Customer',
        'can_be_invoiced' => true,
        'can_be_invoicing' => false,
        'is_business_entity' => false,
    ]);
}
```

### Step 2: Create Individual Customer

```php
<?php

$customer = $client->organizationUsers->create([
    // Required
    'first_name' => 'John',
    'last_name' => 'Doe',
    'country_code' => 'RO',
    'organization_user_role_id' => $customerRole->id,

    // Contact info
    'email' => 'john.doe@example.com',
    'phone_number' => '712345678',
    'phone_number_country_code' => 'RO',

    // Address
    'address_line_1' => 'Strada Principala 123',
    'address_line_2' => 'Apt 4B',
    'address_city' => 'București',
    'address_county' => 'Sector 1',
    'address_zip_code' => '010001',
    'address_iso_3166' => 'RO',

    // Optional
    'locale' => 'ro',
    'birthdate' => '1990-05-15',
]);

echo "Created customer: {$customer->id}\n";
echo "Full name: {$customer->first_name} {$customer->last_name}\n";
```

### Step 3: Create Business Customer

```php
<?php

// First, get or create a business role
$businessRole = $client->userRoles->create([
    'name' => 'Business Customer',
    'can_be_invoiced' => true,
    'can_be_invoicing' => false,
    'is_business_entity' => true,
]);

// Create the business customer
$company = $client->organizationUsers->create([
    // Required for business
    'name' => 'Acme Corporation SRL',
    'country_code' => 'RO',
    'organization_user_role_id' => $businessRole->id,

    // Business details
    'organization_type' => 'SRL',
    'organization_is_eu_vat_registered' => true,

    // Contact
    'email' => 'billing@acme.ro',
    'phone_number' => '212345678',
    'phone_number_country_code' => 'RO',

    // Registered address
    'address_line_1' => 'Bulevardul Unirii 100',
    'address_city' => 'București',
    'address_county' => 'Sector 3',
    'address_zip_code' => '030167',
    'address_iso_3166' => 'RO',

    // Tax ID (CUI/CIF in Romania)
    'tax_id' => 'RO12345678',
    'trade_register_number' => 'J40/1234/2020',
]);

echo "Created company: {$company->name}\n";
```

## Listing Customers

### Basic List

```php
<?php

$users = $client->organizationUsers->list();

foreach ($users as $user) {
    $name = $user->name ?? "{$user->first_name} {$user->last_name}";
    echo "- {$name} ({$user->email})\n";
}
```

### With Pagination

```php
<?php

$users = $client->organizationUsers->list([
    'page' => 1,
    'per_page' => 25,
]);

echo "Total: {$users->getTotalCount()}\n";
echo "Page {$users->getCurrentPage()} of {$users->getTotalPages()}\n";

// Iterate through all pages
while ($users->hasMore()) {
    $users = $client->organizationUsers->list([
        'page' => $users->getCurrentPage() + 1,
        'per_page' => 25,
    ]);

    foreach ($users as $user) {
        // Process user
    }
}
```

### Filtering

```php
<?php

// Customers only (can be invoiced)
$customers = $client->organizationUsers->list([
    'filter' => [
        'role.can_be_invoiced' => true,
    ],
]);

// Search by email
$users = $client->organizationUsers->list([
    'filter' => [
        'email' => 'john@example.com',
    ],
]);

// Business entities only
$companies = $client->organizationUsers->list([
    'filter' => [
        'role.is_business_entity' => true,
    ],
]);
```

### With Related Data

```php
<?php

$users = $client->organizationUsers->list([
    'include' => ['role', 'addresses', 'tags'],
]);

foreach ($users as $user) {
    echo "User: {$user->email}\n";
    echo "Role: {$user->role->name}\n";

    foreach ($user->addresses ?? [] as $address) {
        echo "  Address: {$address->address_line_1}, {$address->address_city}\n";
    }
}
```

## Retrieving a Customer

```php
<?php

$customer = $client->organizationUsers->retrieve('org_user_xxx', [
    'include' => ['role', 'addresses', 'invoices'],
]);

echo "Name: {$customer->first_name} {$customer->last_name}\n";
echo "Email: {$customer->email}\n";
echo "Role: {$customer->role->name}\n";
echo "Invoice count: " . count($customer->invoices ?? []) . "\n";
```

## Updating a Customer

```php
<?php

$customer = $client->organizationUsers->update('org_user_xxx', [
    'email' => 'newemail@example.com',
    'phone_number' => '799999999',
    'address_line_1' => 'New Street 456',
]);

echo "Updated: {$customer->email}\n";
```

## Deleting a Customer

```php
<?php

// Soft delete
$customer = $client->organizationUsers->delete('org_user_xxx');

echo "Deleted at: {$customer->deleted_at}\n";
```

## Restoring a Deleted Customer

```php
<?php

$customer = $client->organizationUsers->restore('org_user_xxx');

echo "Restored: {$customer->email}\n";
```

## Customer Activity

View activity history for a customer:

```php
<?php

$activity = $client->organizationUsers->activity('org_user_xxx', [
    'per_page' => 50,
]);

foreach ($activity as $event) {
    echo "[{$event->created_at}] {$event->description}\n";
}
```

## Tagging Customers

```php
<?php

// Add tags
$customer = $client->organizationUsers->tag('org_user_xxx', [
    'tags' => ['vip', 'wholesale'],
]);

// Remove tags
$customer = $client->organizationUsers->untag('org_user_xxx', [
    'tags' => ['wholesale'],
]);
```

## Managing Addresses

Customers can have multiple addresses (billing, shipping, etc.):

```php
<?php

// Add an address
$address = $client->userAddresses->create([
    'organization_user_id' => 'org_user_xxx',
    'label' => 'Shipping',
    'address_line_1' => 'Warehouse Street 789',
    'address_city' => 'Cluj-Napoca',
    'address_county' => 'Cluj',
    'address_zip_code' => '400000',
    'address_iso_3166' => 'RO',
    'is_default' => false,
]);

// List addresses
$addresses = $client->userAddresses->list([
    'filter' => ['organization_user_id' => 'org_user_xxx'],
]);
```

## Complete Example: Customer Onboarding

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Get customer role
    $roles = $client->userRoles->list([
        'filter' => ['can_be_invoiced' => true, 'is_business_entity' => false],
    ]);
    $customerRole = $roles->data[0];

    // 2. Create customer
    $customer = $client->organizationUsers->create([
        'first_name' => 'Maria',
        'last_name' => 'Popescu',
        'email' => 'maria.popescu@example.com',
        'country_code' => 'RO',
        'organization_user_role_id' => $customerRole->id,
        'address_line_1' => 'Strada Florilor 25',
        'address_city' => 'Timișoara',
        'address_county' => 'Timiș',
        'address_zip_code' => '300001',
        'address_iso_3166' => 'RO',
    ]);

    // 3. Add tags
    $customer = $client->organizationUsers->tag($customer->id, [
        'tags' => ['new-customer', '2026'],
    ]);

    // 4. Add shipping address
    $shippingAddress = $client->userAddresses->create([
        'organization_user_id' => $customer->id,
        'label' => 'Shipping',
        'address_line_1' => 'Depozit Central 100',
        'address_city' => 'Timișoara',
        'address_county' => 'Timiș',
        'address_zip_code' => '300002',
        'address_iso_3166' => 'RO',
    ]);

    echo "Customer created successfully!\n";
    echo "ID: {$customer->id}\n";
    echo "Name: {$customer->first_name} {$customer->last_name}\n";
    echo "Email: {$customer->email}\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Next Steps

Once you have customers (OrganizationUsers), you can:
- [Create invoices](invoices.md) for them
- [Create contracts](contracts.md) with them
- [Send proposals](proposals.md) to them
