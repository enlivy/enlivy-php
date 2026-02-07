# Customer Portal

Allow external customers to access their invoices, contracts, receipts, and reports through authenticated portal sessions.

## Key Concepts

### Portal Session Structure

```
Portal Session
    |
    +-- Organization User (the customer)
    +-- Permissions (what they can access)
    +-- Authentication Method (how they verify identity)
    +-- Expiration (session validity)
```

### Authentication Methods

| Method | Description |
|--------|-------------|
| `email` | Verification code sent via email |
| `phone` | Verification code sent via SMS |
| `magic_authentication` | Direct access without verification |

### Permissions

| Permission | Description |
|------------|-------------|
| `invoices` | View invoices and download PDFs |
| `network_exchanges` | View PEPPOL network exchanges |
| `receipts` | View payment receipts |
| `contracts` | View and download contracts |
| `reports` | View and submit reports |

## Creating a Portal Session

### Basic Session

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$session = $client->userClientPortalSessions->create([
    // Required
    'name' => 'Invoice Access Session',

    // Identify customer (choose one)
    'organization_user_id' => 'org_user_xxx',
    // OR
    'email' => 'customer@example.com',
]);

echo "Session created: {$session->id}\n";
echo "Token: {$session->token}\n";
```

### Session with Permissions

```php
<?php

$session = $client->userClientPortalSessions->create([
    'name' => 'Invoice & Receipts Access',
    'organization_user_id' => 'org_user_xxx',

    // Specific permissions
    'permissions' => [
        'invoices',
        'receipts',
    ],

    // Expiration options (choose one)
    'validity_hours' => 24,           // 24 hours from now
    // OR
    'expires_at' => '2026-02-06T12:00:00Z', // Specific datetime

    // Authentication method
    'authentication_method' => 'email', // email, phone, or magic_authentication
]);

echo "Portal URL: https://portal.yourcompany.com/session/{$session->token}\n";
echo "Expires at: {$session->expires_at}\n";
```

### Session with Full Access

```php
<?php

$session = $client->userClientPortalSessions->create([
    'name' => 'Full Portal Access',
    'organization_user_id' => 'org_user_xxx',

    // Grant all permissions
    'permissions' => [
        'invoices',
        'network_exchanges',
        'receipts',
        'contracts',
        'reports',
    ],

    'validity_hours' => 48,
    'authentication_method' => 'magic_authentication', // No verification needed
]);
```

### Session by Email

```php
<?php

// Find customer by email instead of ID
$session = $client->userClientPortalSessions->create([
    'name' => 'Customer Portal Session',
    'email' => 'customer@example.com', // Must exist as organization user

    'permissions' => ['invoices', 'contracts'],
    'validity_hours' => 2,
    'authentication_method' => 'email',
]);
```

## Listing Portal Sessions

```php
<?php

$sessions = $client->userClientPortalSessions->list([
    'include' => ['organization_user'],
]);

foreach ($sessions as $session) {
    $status = $session->status;
    $expired = $session->expires_at < now() ? ' (expired)' : '';

    echo "Session: {$session->name}{$expired}\n";
    echo "  ID: {$session->id}\n";
    echo "  Status: {$status}\n";
    echo "  Authentication: {$session->authentication_method}\n";
    echo "  Permissions: " . implode(', ', $session->permissions ?? []) . "\n";
    echo "  Last used: {$session->last_used_at}\n";
    echo "  Expires: {$session->expires_at}\n";
}
```

### Filter by User

```php
<?php

$sessions = $client->userClientPortalSessions->list([
    'organization_user_id' => 'org_user_xxx',
    'include' => ['organization_user'],
]);
```

## Retrieving a Session

```php
<?php

$session = $client->userClientPortalSessions->retrieve('org_user_cp_sess_xxx', [
    'include' => ['organization_user', 'organization'],
]);

echo "Session: {$session->name}\n";
echo "Token: {$session->token}\n";
echo "Status: {$session->status}\n";
echo "Authentication Method: {$session->authentication_method}\n";
echo "Permissions: " . implode(', ', $session->permissions ?? []) . "\n";
echo "Customer: {$session->organization_user->email}\n";
echo "Expires: {$session->expires_at}\n";
echo "Last used: {$session->last_used_at}\n";
```

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Session name/description |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `organization_user_id` | string | Customer's organization user ID |
| `email` | string | Customer's email (alternative to user ID) |
| `permissions` | array | Access permissions (invoices, receipts, contracts, reports, network_exchanges) |
| `validity_hours` | integer | Hours until expiration (max 7 days = 168 hours) |
| `expires_at` | datetime | Specific expiration datetime (max 7 days from now) |
| `authentication_method` | string | Verification method (email, phone, magic_authentication) |

Note: Either `organization_user_id` or `email` must be provided to identify the customer.

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Session ID |
| `organization_id` | string | Organization ID |
| `organization_user_id` | string | Customer user ID |
| `name` | string | Session name |
| `token` | string | Session token for URL |
| `status` | string | Session status |
| `authentication_method` | string | How user authenticates |
| `authentication_verification_code` | string | Current verification code (if applicable) |
| `permissions` | array | Granted permissions |
| `last_used_at` | datetime | Last access timestamp |
| `expires_at` | datetime | Expiration timestamp |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |

### Include Options

| Include | Description |
|---------|-------------|
| `organization` | Organization details |
| `organization_user` | Customer user details |

## Complete Example: Invoice Payment Link

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Get the customer
    $customer = $client->organizationUsers->retrieve('org_user_xxx');

    // 2. Create a portal session for invoice access
    $session = $client->userClientPortalSessions->create([
        'name' => "Invoice access for {$customer->first_name}",
        'organization_user_id' => $customer->id,

        // Only invoice access
        'permissions' => ['invoices'],

        // 2 hour validity
        'validity_hours' => 2,

        // Skip verification for one-click access
        'authentication_method' => 'magic_authentication',
    ]);

    // 3. Build the portal URL
    $portalUrl = "https://portal.yourcompany.com/session/{$session->token}";

    echo "Portal session created!\n";
    echo "Send this URL to your customer: {$portalUrl}\n";
    echo "Valid until: {$session->expires_at}\n";

    // 4. You could now send this URL via email
    // sendEmail($customer->email, 'View Your Invoices', $portalUrl);

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Complete Example: Report Submission Portal

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // Create session for team member to submit reports
    $session = $client->userClientPortalSessions->create([
        'name' => 'Weekly Report Submission',
        'email' => 'team-member@example.com',

        // Reports permission only
        'permissions' => ['reports'],

        // 1 week validity
        'validity_hours' => 168,

        // Require email verification
        'authentication_method' => 'email',
    ]);

    echo "Report submission portal created!\n";
    echo "Session ID: {$session->id}\n";
    echo "Authentication: {$session->authentication_method}\n";
    echo "Expires: {$session->expires_at}\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Security Best Practices

1. **Use minimal permissions** - Only grant access to what's needed
2. **Short expiration** - Use short session durations for sensitive data
3. **Require verification** - Use `email` or `phone` authentication for security
4. **Track usage** - Monitor `last_used_at` for suspicious activity
5. **Audit sessions** - Regularly review active sessions
6. **Named sessions** - Use descriptive names to track session purposes

## Related

- [Organization Users](organization-users.md) - Customer accounts
- [Invoices](invoices.md) - Customer invoices
- [Contracts](contracts.md) - Customer contracts
- [Reports](reports.md) - Customer reports
- [Receipts](receipts.md) - Payment receipts
