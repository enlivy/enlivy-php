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
| `can_use_backoffice` | User can access the backoffice |
| `can_be_payrolled` | User can receive payslips |
| `is_business_entity` | User is a company (vs individual) |

### Business Entity vs Individual

The `is_business_entity` flag on a user role controls how the user is treated across the system. It is **not** a field on the user itself — it is set on the `OrganizationUserRole` and exposed as a read-only `is_business_entity` boolean on each user response.

This affects:

1. **Required billing fields** — the API validates different fields depending on `is_business_entity`:

   | Field | Individual (`false`) | Business (`true`) |
   |-------|---------------------|-------------------|
   | `first_name` | Required | Optional |
   | `last_name` | Required | Optional |
   | `name` | Optional | Required |
   | `organization_type` | Not needed | Required |
   | `organization_information` | Not needed | Required |

2. **UI display** — use `is_business_entity` to decide what to show:

```php
if ($user->is_business_entity) {
    echo "Company: {$user->name}\n";
    echo "Type: {$user->organization_type}\n";
} else {
    echo "Name: {$user->first_name} {$user->last_name}\n";
}
```

### Avatar

Each user has an `avatar_url` in the API response. The URL is resolved in this order:

1. **Uploaded avatar** — if the user has a custom avatar (512×512 JPEG)
2. **Gravatar** — if the user has an email, falls back to `gravatar.com` with the `mp` (mystery person) default
3. **`null`** — no email and no uploaded avatar

To upload an avatar, pass a `CURLFile` in the create or update params:

```php
// Upload avatar on create
$user = $client->organizationUsers->create([
    'organization_user_role_id' => $roleId,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'country_code' => 'RO',
    'avatar' => new \CURLFile('/path/to/photo.jpg', 'image/jpeg', 'avatar.jpg'),
]);

echo $user->avatar_url; // https://storage.enlivy.com/.../org_user_xxx.jpg

// Upload avatar on update
$user = $client->organizationUsers->update('org_user_xxx', [
    'avatar' => new \CURLFile('/path/to/new-photo.png', 'image/png', 'avatar.png'),
]);

// Remove avatar (set to null — reverts to Gravatar fallback)
$user = $client->organizationUsers->update('org_user_xxx', [
    'avatar' => null,
]);
```

Accepted formats: JPEG, PNG, GIF, WebP. Max size: 10 MB. The API processes the image to 512×512 JPEG.

> **Note:** When sending a `CURLFile`, the SDK automatically switches from JSON to multipart form data. All other fields in the same request are sent as form fields.

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
    'can_be_invoiced' => true,
    'is_business_entity' => false, // for individuals
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
    // Required fields
    'organization_user_role_id' => $customerRole->id,
    'country_code' => 'RO',

    // Required for individual (non-business) role
    'first_name' => 'John',
    'last_name' => 'Doe',

    // Contact info (optional)
    'email' => 'john.doe@example.com',
    'phone_number' => '712345678',
    'phone_number_country_code' => 'RO',

    // Address fields (optional)
    'address_line_1' => 'Strada Principala 123',
    'address_line_2' => 'Apt 4B',
    'address_city' => 'Bucuresti',
    'address_county' => 'Sector 1',
    'address_zip_code' => '010001',
    'address_iso_3166' => 'RO-B', // ISO 3166-2 subdivision code

    // Optional settings
    'locale' => 'ro',
    'timezone' => 'Europe/Bucharest',
    'birthdate' => '1990-05-15',

    // Country-specific information (JSON object)
    'information' => [
        'cnp' => '1900515123456', // Romanian personal ID
    ],
]);

echo "Created customer: {$customer->id}\n";
echo "Full name: {$customer->first_name} {$customer->last_name}\n";
```

### Step 3: Create Business Customer

```php
<?php

// First, get or create a business role
$businessRoles = $client->userRoles->list([
    'can_be_invoiced' => true,
    'is_business_entity' => true,
]);

$businessRole = $businessRoles->data[0] ?? $client->userRoles->create([
    'name' => 'Business Customer',
    'can_be_invoiced' => true,
    'can_be_invoicing' => false,
    'is_business_entity' => true,
]);

// Create the business customer
$company = $client->organizationUsers->create([
    // Required fields
    'organization_user_role_id' => $businessRole->id,
    'country_code' => 'RO',

    // Required for business entity role
    'name' => 'Acme Corporation SRL',

    // Business details
    'organization_type' => 'SRL', // Company type (SRL, SA, PFA, etc.)

    // Organization-specific information (JSON object with tax IDs, registration numbers)
    'organization_information' => [
        'cui' => '12345678',              // Tax ID (CUI in Romania)
        'registration_number' => 'J40/1234/2020', // Trade register number
        'vat_number' => 'RO12345678',     // VAT number if registered
    ],

    // Contact info
    'email' => 'billing@acme.ro',
    'phone_number' => '212345678',
    'phone_number_country_code' => 'RO',

    // Registered address
    'address_line_1' => 'Bulevardul Unirii 100',
    'address_city' => 'Bucuresti',
    'address_county' => 'Sector 3',
    'address_zip_code' => '030167',
    'address_iso_3166' => 'RO-B',

    // Optional settings
    'locale' => 'ro',
    'timezone' => 'Europe/Bucharest',
]);

echo "Created company: {$company->name}\n";
```

### Customer with Bank Account Details

```php
<?php

$customer = $client->organizationUsers->create([
    'organization_user_role_id' => $businessRole->id,
    'country_code' => 'RO',
    'name' => 'Supplier Company SRL',
    'organization_type' => 'SRL',

    // Bank account fields (for receiving payments)
    'bank_account_bank_name' => 'Banca Transilvania',
    'bank_account_type' => 'standard', // standard, stripe_external
    'bank_account_currency' => 'EUR',
    'bank_account_country_code' => 'RO',
    'bank_account_address' => 'Str. George Baritiu 8, Cluj-Napoca',

    // Bank account details (JSON based on type)
    'bank_account_information' => [
        'iban' => 'RO49BTRLEURCRT0123456789',
        'swift' => 'BTRLRO22',
    ],

    // Contact
    'email' => 'finance@supplier.ro',
]);
```

### Customer with Stripe Integration

```php
<?php

$customer = $client->organizationUsers->create([
    'organization_user_role_id' => $customerRole->id,
    'country_code' => 'US',
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'email' => 'jane.smith@example.com',

    // Link to Stripe customer (optional)
    'payment_stripe_customer_id' => 'cus_xxx',
    'payment_stripe_customer_ids' => ['cus_xxx'], // Array when multiple Stripe accounts
]);
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
    'role.can_be_invoiced' => true,
]);

// Search by email
$users = $client->organizationUsers->list([
    'email' => 'john@example.com',
]);

// Business entities only
$companies = $client->organizationUsers->list([
    'role.is_business_entity' => true,
]);

// By country
$romanianUsers = $client->organizationUsers->list([
    'country_code' => 'RO',
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

// Display based on whether it's business or individual
if ($customer->role->is_business_entity) {
    echo "Company: {$customer->name}\n";
    echo "Type: {$customer->organization_type}\n";
    if (isset($customer->organization_information['cui'])) {
        echo "Tax ID: {$customer->organization_information['cui']}\n";
    }
} else {
    echo "Name: {$customer->first_name} {$customer->last_name}\n";
}

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

### Update Organization Information

```php
<?php

$company = $client->organizationUsers->update('org_user_xxx', [
    'organization_information' => [
        'cui' => '87654321',
        'registration_number' => 'J40/5678/2024',
        'vat_number' => 'RO87654321',
    ],
]);
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
    'country_code' => 'RO',
    'address_iso_3166' => 'RO-CJ',
    'is_default' => false,
]);

// List addresses
$addresses = $client->userAddresses->list([
    'organization_user_id' => 'org_user_xxx',
]);
```

## Field Reference

### Required Fields

| Field | Individual | Business | Description |
|-------|------------|----------|-------------|
| `organization_user_role_id` | Yes | Yes | Role ID from userRoles |
| `country_code` | Yes | Yes | ISO 3166-1 alpha-2 code |
| `first_name` | Yes | No | First name (non-business) |
| `last_name` | Yes | No | Last name (non-business) |
| `name` | No | Yes | Company name (business) |

### Read-Only Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `is_business_entity` | bool | Whether the user's role is a business entity. Derived from the assigned `OrganizationUserRole` |
| `avatar_url` | string\|null | Avatar URL (uploaded image, Gravatar fallback, or null) |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `avatar` | file (CURLFile) | Avatar image (JPEG/PNG/GIF/WebP, max 10 MB). Set to `null` to remove |
| `email` | string | Email address (unique per org) |
| `phone_number` | string | Phone number |
| `phone_number_country_code` | string | Phone country code |
| `address_line_1` | string | Street address line 1 |
| `address_line_2` | string | Street address line 2 |
| `address_city` | string | City |
| `address_county` | string | County/Province |
| `address_state` | string | State |
| `address_zip_code` | string | Postal/ZIP code |
| `address_iso_3166` | string | ISO 3166-2 subdivision code |
| `locale` | string | Language locale (e.g., 'en', 'ro') |
| `timezone` | string | Timezone (e.g., 'Europe/Bucharest') |
| `birthdate` | string | Birth date (YYYY-MM-DD) |
| `organization_type` | string | Company type (SRL, SA, etc.) |
| `information` | object | Country-specific personal info (JSON) |
| `organization_information` | object | Company info (tax ID, etc.) (JSON) |
| `bank_account_bank_name` | string | Bank name |
| `bank_account_type` | string | 'standard' or 'stripe_external' |
| `bank_account_currency` | string | ISO 4217 currency code |
| `bank_account_country_code` | string | Bank country code |
| `bank_account_information` | object | Bank details (IBAN, SWIFT, etc.) |
| `bank_account_address` | string | Bank address |
| `payment_stripe_customer_id` | string | Primary Stripe customer ID |
| `payment_stripe_customer_ids` | array | All linked Stripe customer IDs |

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
        'can_be_invoiced' => true,
        'is_business_entity' => false,
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
        'address_city' => 'Timisoara',
        'address_county' => 'Timis',
        'address_zip_code' => '300001',
        'address_iso_3166' => 'RO-TM',
        'locale' => 'ro',
        'timezone' => 'Europe/Bucharest',
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
        'address_city' => 'Timisoara',
        'address_county' => 'Timis',
        'address_zip_code' => '300002',
        'country_code' => 'RO',
        'address_iso_3166' => 'RO-TM',
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
