# Customer Portal

Allow external customers to access their invoices, contracts, and documents.

## Concepts

- **Portal Session**: Temporary authenticated access for a customer
- **Portal Domain**: Custom domain for your customer portal

## Creating a Portal Session

Generate a session for a customer to access their portal:

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$session = $client->userClientPortalSessions->create([
    'organization_user_id' => 'org_user_xxx',
    'expires_in' => 3600, // 1 hour
    'return_url' => 'https://yoursite.com/dashboard',
]);

echo "Portal URL: {$session->url}\n";
echo "Expires at: {$session->expires_at}\n";

// Redirect customer to this URL
header("Location: {$session->url}");
```

## Portal Session Options

```php
<?php

$session = $client->userClientPortalSessions->create([
    'organization_user_id' => 'org_user_xxx',
    'expires_in' => 7200, // 2 hours

    // Customize what the customer can see
    'allowed_sections' => ['invoices', 'contracts', 'payments'],

    // Where to return after portal
    'return_url' => 'https://yoursite.com/account',

    // Optional: Pre-select a specific item
    'initial_path' => '/invoices/org_inv_xxx',
]);
```

## Listing Sessions

```php
<?php

$sessions = $client->userClientPortalSessions->list([
    'filter' => [
        'organization_user_id' => 'org_user_xxx',
    ],
]);

foreach ($sessions as $session) {
    $status = strtotime($session->expires_at) > time() ? 'Active' : 'Expired';
    echo "Session {$session->id}: {$status}\n";
}
```

## Custom Portal Domains

Configure a custom domain for your portal.

### Set Portal Domain

```php
<?php

$domain = $client->userPortalDomain->update([
    'domain' => 'portal.yourcompany.com',
    'is_active' => true,
]);

echo "Portal domain: {$domain->domain}\n";
```

### Get Portal Domain

```php
<?php

$domain = $client->userPortalDomain->retrieve();

echo "Domain: {$domain->domain}\n";
echo "SSL Status: {$domain->ssl_status}\n";
```

## Complete Example: Customer Invoice Access

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

// Scenario: Customer clicks "View Invoice" in an email

$customerId = 'org_user_xxx';
$invoiceId = 'org_inv_xxx';

// 1. Create a portal session
$session = $client->userClientPortalSessions->create([
    'organization_user_id' => $customerId,
    'expires_in' => 3600,
    'initial_path' => "/invoices/{$invoiceId}",
    'return_url' => 'https://yoursite.com',
]);

// 2. Redirect to portal
header("Location: {$session->url}");
exit;

// Customer can now:
// - View their invoice
// - Download PDF
// - Make payment (if Stripe connected)
// - View payment history
```

## Security Best Practices

1. **Short expiration** - Use short session durations (1-2 hours)
2. **Specific access** - Only enable sections the customer needs
3. **HTTPS only** - Always use SSL for portal domains
4. **Log access** - Track portal session creation for audit

## Related

- [Organization Users](organization-users.md) - Customer accounts
- [Invoices](invoices.md) - Customer invoices
- [Integrations](integrations.md) - Payment integrations
