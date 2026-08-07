# Enlivy PHP SDK

[![CI](https://github.com/enlivy/enlivy-php/actions/workflows/ci.yml/badge.svg)](https://github.com/enlivy/enlivy-php/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/enlivy/enlivy-php.svg)](https://packagist.org/packages/enlivy/enlivy-php)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

Official PHP client library for the [Enlivy API](https://enlivy.com). Follows
[Semantic Versioning](https://semver.org/); breaking changes ship only in major
versions, with migration notes in [UPGRADING.md](UPGRADING.md).

## Requirements

- PHP 8.3+
- `ext-curl`, `ext-json`, `ext-mbstring`

## Installation

```bash
composer require enlivy/enlivy-php
```

## Quick Start

```php
$client = new \Enlivy\EnlivyClient([
    'api_key' => '1|your_api_token',
    'organization_id' => 'org_xxx',
]);

// List invoices
$invoices = $client->invoices->list(['per_page' => 25]);

foreach ($invoices as $invoice) {
    echo $invoice->id . "\n";
}

// Create
$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
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

// Retrieve with related data
$invoice = $client->invoices->retrieve('org_inv_xxx', [
    'include' => ['sender_user', 'receiver_user', 'line_items'],
]);

// Update
$client->invoices->update('org_inv_xxx', ['status' => 'pending']);

// Delete
$client->invoices->delete('org_inv_xxx');
```

## Configuration

```php
// Per-client configuration
$client = new \Enlivy\EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
    'api_base' => 'https://api.enlivy.com',
    'timeout' => 30,
]);

// Or global configuration
\Enlivy\Enlivy::setApiKey('1|your_token');
\Enlivy\Enlivy::setOrganizationId('org_xxx');
$client = new \Enlivy\EnlivyClient();
```

## Documentation

Detailed guides with code examples for every feature:

### Getting Started

| Guide | Description |
|-------|-------------|
| [Authentication](docs/authentication.md) | API keys, OAuth client credentials, global config |
| [OAuth Server](docs/oauth.md) | OAuth 2.0 server for third-party app integrations |
| [Includes (Eager Loading)](docs/includes.md) | Load related resources in a single request |
| [Filters](docs/filters.md) | Search, sort, paginate, and filter list endpoints |
| [Sandboxes](docs/sandboxes.md) | Mirror an organization to test against, with outbound calls blocked |

### Billing & Invoicing

| Guide | Description |
|-------|-------------|
| [Invoices](docs/organization/invoices.md) | Create, send, charge, and chase invoices, including scheduled payment reminders |
| [Receipts](docs/organization/receipts.md) | Receipt management and tracking |
| [Billing Packages](docs/organization/billing-packages.md) | Reusable billing templates with payment plans |
| [Proposals](docs/organization/proposals.md) | Send proposals to prospects and customers |
| [Products](docs/organization/products.md) | Product and service catalog |
| [Taxes](docs/organization/taxes.md) | Tax classes and rates, plus the compliance engine: registrations, the tax-event subledger, and filing periods |

### CRM & Sales

| Guide | Description |
|-------|-------------|
| [Prospects](docs/organization/prospects.md) | Sales pipeline, lead tracking, and CRM |
| [Organization Users](docs/organization/users.md) | Customers, employees, and roles |
| [Projects](docs/organization/projects.md) | Projects, team members, and permissions |

### Contracts

| Guide | Description |
|-------|-------------|
| [Contracts](docs/organization/contracts.md) | Contract management, e-signatures, templates, and what references a contract |

### Banking

| Guide | Description |
|-------|-------------|
| [Bank Accounts](docs/organization/bank-accounts.md) | Bank accounts, transactions, and reconciliation |

### Content & Reports

| Guide | Description |
|-------|-------------|
| [Reports](docs/organization/reports.md) | Dynamic reports with custom schemas |
| [Files](docs/organization/files.md) | File uploads and attachments |
| [Data Imports](docs/organization/data-imports.md) | Bulk-load products, users, prospects, and transactions from CSV |

### Integrations

| Guide | Description |
|-------|-------------|
| [Event Destinations](docs/organization/event-destinations.md) | Real-time event delivery (webhooks, Slack) and signature verification |
| [Event Trails](docs/organization/event-trails.md) | Read-only audit history for invoices, receipts, and billing schedules |
| [Customer Portal](docs/organization/customer-portal.md) | Client-facing portal for invoices, contracts, and proposals |
| [Integrations](docs/integrations.md) | Stripe, ANAF, and other third-party services |
| [AI Agents](docs/ai-agents.md) | AI-powered automation |

## Error Handling

```php
use Enlivy\Exception\{
    ValidationException,
    NotFoundException,
    AuthenticationException,
    RateLimitException,
};

try {
    $invoice = $client->invoices->retrieve('org_inv_xxx');
} catch (ValidationException $e) {
    $errors = $e->errors(); // ['field' => ['error message']]
} catch (NotFoundException $e) {
    // 404
} catch (AuthenticationException $e) {
    // 401
} catch (RateLimitException $e) {
    $retryAfter = $e->retryAfter(); // seconds
}
```

## Pagination

```php
$invoices = $client->invoices->list(['page' => 1, 'per_page' => 25]);

echo "Page " . $invoices->getCurrentPage() . " of " . $invoices->getTotalPages();

foreach ($invoices as $invoice) {
    echo $invoice->id;
}

// Or iterate every item across all pages; follow-up pages are fetched lazily
foreach ($invoices->autoPagingIterator() as $invoice) {
    echo $invoice->id;
}
```

## Request Options

Every service method accepts an optional `RequestOptions` as its last argument:

```php
use Enlivy\Util\RequestOptions;

$invoice = $client->invoices->create($params, new RequestOptions(
    organizationId: 'org_other',      // per-request organization override
    idempotencyKey: 'idem_xyz',       // safe write retries
    locale: 'ro',                     // Accept-Language for localized fields
    timeout: 60,                      // per-request timeout (seconds)
    headers: ['X-Custom' => 'value'], // extra headers
));
```

## Retries

Transient failures (connection errors, `429`, `5xx`) are retried automatically
with exponential backoff — for `GET` requests, and for writes that carry an
`Idempotency-Key`. Configure via `max_retries` on the client (default `2`, `0`
disables) or globally with `Enlivy::setMaxNetworkRetries()`.

## Response Metadata

Every object the SDK returns carries the raw response it was hydrated from —
status code, headers, and the decoded body — via `lastResponse()`. Reach for it
when an endpoint returns data alongside the resource in `meta`, such as the
inline first-charge result on a newly created billing schedule:

```php
$schedule = $client->billingSchedules->fromBillingPackage([/* … */]);

$response = $schedule->lastResponse();
$response?->statusCode;                 // 201
$response?->getHeader('X-Request-Id');
$response?->json['meta']['charge_result'] ?? null;
```

## API Discovery

The SDK includes a discovery service for programmatic API introspection:

```php
// List all available API resources
$resources = $client->discovery->list();

// Get detailed metadata for a specific resource
$invoiceSpec = $client->discovery->resource('organization_invoices');
```

## Key Concepts

### Multilingual Fields

Most text fields use `_lang_map` for multilingual support:

```php
'name_lang_map' => [
    'en' => 'Consulting Services',
    'ro' => 'Servicii de Consultanta',
],
```

### ID Prefixes

All IDs use prefixes to identify the resource type:

| Prefix | Resource |
|--------|----------|
| `org_` | Organization |
| `org_user_` | Organization User |
| `org_inv_` | Invoice |
| `org_cont_` | Contract |
| `org_pros_` | Prospect |
| `org_proj_` | Project |
| `org_prod_` | Product |
| `org_prop_` | Proposal |

## Testing

```bash
./vendor/bin/phpunit              # Unit tests
./vendor/bin/phpstan analyse      # Static analysis
```

## License

MIT License. See [LICENSE](LICENSE) for details.

## Support

- [API Documentation](https://docs.enlivy.com/api)
- [Issues](https://github.com/enlivy/enlivy-php/issues)
