# Enlivy PHP SDK

Official PHP client library for the [Enlivy API](https://enlivy.com).

## Requirements

- PHP 8.3 or later
- `ext-curl`
- `ext-json`
- `ext-mbstring`

## Installation

```bash
composer require enlivy/enlivy-php
```

## Quick Start

### API Key Authentication

```php
$enlivy = new \Enlivy\EnlivyClient([
    'api_key' => '1|your_api_token',
    'organization_id' => 'org_xxx', // Default org for all requests
]);

// List prospects
$prospects = $enlivy->prospects->list(['per_page' => 25]);

foreach ($prospects as $prospect) {
    echo $prospect->title . "\n";
}

// Create a prospect
$prospect = $enlivy->prospects->create([
    'title' => 'New Lead',
    'email' => 'lead@example.com',
]);

// Retrieve a prospect
$prospect = $enlivy->prospects->retrieve('org_pros_xxx');

// Update a prospect
$enlivy->prospects->update('org_pros_xxx', [
    'title' => 'Updated Title',
]);

// Delete a prospect
$enlivy->prospects->delete('org_pros_xxx');
```

### OAuth Authentication

```php
$enlivy = new \Enlivy\EnlivyClient([
    'access_token' => 'eat_xxx',
    'refresh_token' => 'ert_xxx',
    'client_id' => 'oac_xxx',
    'client_secret' => 'your_client_secret',
    'organization_id' => 'org_xxx',
    'on_token_refresh' => function (string $accessToken, ?string $refreshToken) {
        // Save the new tokens to your database
        saveTokens($accessToken, $refreshToken);
    },
]);
```

The SDK automatically refreshes tokens on 401 responses and calls your callback with new tokens.

## Configuration Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `api_key` | string | Yes* | Sanctum API token (format: `{id}\|{token}`) |
| `access_token` | string | Yes* | OAuth access token |
| `refresh_token` | string | No | OAuth refresh token for auto-refresh |
| `client_id` | string | No | OAuth client ID |
| `client_secret` | string | No | OAuth client secret |
| `organization_id` | string | No | Default organization ID for all requests |
| `api_base` | string | No | Custom API base URL (default: `https://api.enlivy.com`) |
| `timeout` | int | No | Request timeout in seconds (default: 30) |
| `http_client` | HttpClientInterface | No | Custom HTTP client |

*Either `api_key` OR `access_token` is required.

## Organization Scoping

Most Enlivy resources are organization-scoped. You can set a default organization or override per-request:

```php
// Set default organization in constructor
$enlivy = new EnlivyClient([
    'api_key' => '1|token',
    'organization_id' => 'org_default',
]);

// Uses org_default
$prospects = $enlivy->prospects->list();

// Override per-request via params
$prospects = $enlivy->prospects->list([
    'organization_id' => 'org_other',
]);

// Override per-request via RequestOptions
$opts = new \Enlivy\Util\RequestOptions(organizationId: 'org_other');
$prospects = $enlivy->prospects->list([], $opts);
```

## Nested Resources

Nested resources use flat service names with the parent ID as the first parameter:

```php
// Project members
$members = $enlivy->projectMembers->list('org_proj_xxx');
$member = $enlivy->projectMembers->create('org_proj_xxx', [
    'user_id' => 'org_user_xxx',
    'role' => 'member',
]);

// User addresses
$addresses = $enlivy->userAddresses->list('org_user_xxx');

// Report schema fields
$fields = $enlivy->reportSchemaFields->list('org_rptschm_xxx');
```

## Typed Resources

The SDK returns typed resource objects with full IDE autocompletion support:

```php
use Enlivy\Resource\Prospect;
use Enlivy\Resource\Invoice;

// Methods return typed objects
$prospect = $enlivy->prospects->retrieve('org_pros_xxx');
// $prospect is Prospect - IDE knows about ->title, ->email, etc.

$invoice = $enlivy->invoices->retrieve('org_inv_xxx');
// $invoice is Invoice - IDE knows about ->total, ->status, ->currency, etc.

// Collections contain typed items
$prospects = $enlivy->prospects->list();
foreach ($prospects as $prospect) {
    // $prospect is Prospect
    echo $prospect->title . ' - ' . $prospect->email;
}

// First/last helpers
$first = $prospects->first(); // Prospect|null
$last = $prospects->last();   // Prospect|null
```

All 55+ resource types have full `@property` PHPDoc annotations for IDE support.

## Pagination

List endpoints return a `Collection` that provides pagination info:

```php
$prospects = $enlivy->prospects->list(['page' => 1, 'per_page' => 25]);

echo "Page " . $prospects->getCurrentPage() . " of " . $prospects->getTotalPages();
echo "Total records: " . $prospects->getTotalCount();

if ($prospects->hasMore()) {
    $nextPage = $enlivy->prospects->list(['page' => 2]);
}

// Iterate over items (typed as Prospect)
foreach ($prospects as $prospect) {
    echo $prospect->title;
}
```

## Error Handling

The SDK throws typed exceptions for API errors:

```php
use Enlivy\Exception\{
    AuthenticationException,
    ForbiddenException,
    NotFoundException,
    ValidationException,
    RateLimitException,
    ServerException,
    ApiConnectionException,
};

try {
    $prospect = $enlivy->prospects->retrieve('org_pros_invalid');
} catch (ValidationException $e) {
    // 422 - Get field errors
    $errors = $e->errors();
    // ['email' => ['The email field is required.']]
} catch (NotFoundException $e) {
    // 404
    echo "Prospect not found";
} catch (AuthenticationException $e) {
    // 401
    echo "Invalid credentials";
} catch (RateLimitException $e) {
    // 429
    $retryAfter = $e->retryAfter(); // seconds
} catch (ServerException $e) {
    // 500+
    echo "Server error, try again later";
}
```

## Webhook Signature Verification

Verify webhook signatures to ensure requests are from Enlivy:

```php
use Enlivy\Webhook\WebhookEvent;
use Enlivy\Exception\InvalidArgumentException;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_ENLIVY_SIGNATURE'];
$secret = 'whsec_your_webhook_secret';

try {
    $event = WebhookEvent::constructFrom($payload, $signature, $secret);

    switch ($event->type) {
        case 'invoice.created':
            $invoice = $event->data;
            // Handle new invoice
            break;
        case 'prospect.updated':
            $prospect = $event->data;
            // Handle prospect update
            break;
    }
} catch (InvalidArgumentException $e) {
    // Invalid signature
    http_response_code(400);
    exit;
}
```

## File Downloads

Some resources support file downloads:

```php
// Download invoice PDF
$pdf = $enlivy->invoices->download('org_inv_xxx');
file_put_contents('invoice.pdf', $pdf);

// Download contract PDF
$pdf = $enlivy->contracts->download('org_cont_xxx');

// Export data
$csv = $enlivy->exportData->download('org_exprt_xxx');
```

## Available Services

### Non-Organization-Scoped

- `authentication` - Login, logout, password reset
- `organizations` - Organization management
- `users` - User accounts
- `userTokens` - API tokens
- `aiAgents` - AI agents (read-only + run; CRUD requires admin)
- `oauthClients` - OAuth applications
- `oauthAuthorizations` - OAuth authorizations
- `oauthTokens` - OAuth tokens
- `invitationCodes` - Invitation codes
- `serviceIntegration` - Third-party integrations
- `frontend` - Frontend configuration
- `userOrganizationSettings` - Per-org user settings

> **Note:** Some resources (`membershipPlans`, `userAccountTypes`) require administrator privileges and are not included in this SDK.

### Organization-Scoped

**CRM**
- `prospects` - Leads and prospects
- `prospectActivities` - Prospect activity log
- `prospectStatuses` - Custom prospect statuses
- `projects` - Projects
- `projectMembers` - Project team members
- `projectPermissionProspects/Guidelines/Playbooks/Reports` - Project permissions
- `projectProspectStatuses` - Project-specific prospect statuses

**Users & Roles**
- `organizationUsers` - Organization members
- `userRoles` - Custom roles
- `userRoleAbilities` - Role permissions
- `userAddresses` - User addresses

**Accounting**
- `invoices` - Invoices (with download, email, PEPPOL)
- `invoicePrefixes` - Invoice numbering
- `invoiceNotificationLogs` - Email logs
- `invoiceNetworkExchanges` - E-invoicing
- `receipts` - Receipts (with download)
- `receiptPrefixes` - Receipt numbering
- `products` - Product catalog
- `bankAccounts` - Bank accounts
- `bankTransactions` - Bank transactions
- `bankTransactionCostTypes` - Cost categories
- `bankAccountData` - Bank data sync
- `billingSchedules` - Recurring billing

**Contracts**
- `contracts` - Contracts (with download, signatures)
- `contractPrefixes` - Contract numbering
- `contractStatuses` - Custom statuses
- `contractSignatures` - Digital signatures

**Tax**
- `taxClasses` - Tax classifications
- `taxRates` - Tax rates
- `taxTypes` - Tax types
- `taxFilingJurisdictions` - Tax jurisdictions

**Payroll**
- `payslipSchemas` - Payslip templates
- `payslips` - Employee payslips

**Reports**
- `reports` - Custom reports
- `reportSchemas` - Report templates
- `reportSchemaFields` - Report fields

**Content & Files**
- `files` - File management
- `guidelines` - Guidelines (with download)
- `playbooks` - Playbooks (with download)
- `reusableContent` - Content snippets
- `emailPreviews` - Email preview

**Tasks**
- `tasks` - Task management
- `taskStatuses` - Task statuses

**Settings**
- `tags` - Tags
- `settings` - Organization settings
- `preferences` - User preferences
- `notifications` - Notifications

**Webhooks & Export**
- `webhooks` - Webhook endpoints
- `exportData` - Data export (with download)

**Portal**
- `userClientPortalSessions` - Customer portal sessions
- `userPortalDomain` - Portal domain settings

**Membership & Billing**
- `membership` - Subscription management
- `offers` - Offer templates
- `proposals` - Sales proposals

**Search & AI**
- `search` - Full-text search
- `match` - AI matching
- `misc` - Miscellaneous utilities
- `analytics` - Analytics data

**API & Integrations**
- `apiCredentials` - API credentials
- `resourceBundles` - Resource bundles
- `resourceBundlePermissionGuidelines/Playbooks/Reports` - Bundle permissions
- `stripeWebhookCallbacks` - Stripe webhooks
- `serviceIntegrationAnaf` - ANAF integration (Romania)
- `serviceIntegrationStripe` - Stripe integration

## Testing

```bash
composer install
./vendor/bin/phpunit
./vendor/bin/phpstan analyse
```

## Custom HTTP Client

You can provide your own HTTP client (e.g., Guzzle):

```php
use Enlivy\HttpClient\HttpClientInterface;

class GuzzleClient implements HttpClientInterface
{
    // Implement request() and requestRaw()
}

$enlivy = new EnlivyClient([
    'api_key' => '1|token',
    'http_client' => new GuzzleClient(),
]);
```

## License

MIT License. See [LICENSE](LICENSE) for details.
