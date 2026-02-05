# Enlivy PHP SDK Examples

This directory contains usage examples for the Enlivy PHP SDK.

## Installation

```bash
composer require enlivy/enlivy-php
```

## Quick Start

```php
<?php

require 'vendor/autoload.php';

// Option 1: Global configuration (Stripe-style)
\Enlivy\Enlivy::setApiKey('1|your_api_token');
\Enlivy\Enlivy::setOrganizationId('org_xxx');

$client = new \Enlivy\EnlivyClient();

// Option 2: Per-client configuration
$client = new \Enlivy\EnlivyClient([
    'api_key' => '1|your_api_token',
    'organization_id' => 'org_xxx',
]);

// Now use the client
$invoices = $client->invoices->list();
```

## Documentation by Topic

### Getting Started
- [Authentication](authentication.md) - API keys, OAuth, configuration

### Customer Management
- [Organization Users](organization-users.md) - Creating and managing customers/clients
- [Prospects](prospects.md) - Sales pipeline and CRM

### Billing & Invoicing
- [Invoices](invoices.md) - Creating and sending invoices
- [Receipts](receipts.md) - Receipt management
- [Products](products.md) - Product catalog
- [Taxes](taxes.md) - Tax configuration

### Contracts & Proposals
- [Contracts](contracts.md) - Contract management and e-signatures
- [Proposals](proposals.md) - Sales proposals from offer templates

### Project Management
- [Projects](projects.md) - Projects and team management

### Banking
- [Bank Accounts](bank-accounts.md) - Bank accounts and transactions

### Reports & Files
- [Reports](reports.md) - Dynamic reports and schemas
- [Files](files.md) - File uploads and attachments

### Integrations
- [Webhooks](webhooks.md) - Webhook endpoints and event handling
- [OAuth](oauth.md) - OAuth server for third-party apps
- [Integrations](integrations.md) - Stripe, ANAF, and other integrations

### Advanced
- [Customer Portal](customer-portal.md) - External user access
- [AI Agents](ai-agents.md) - AI-powered features

## Namespace Structure

The SDK uses a clear separation between global and organization-scoped resources:

```php
// Global resources (accounts, not tied to an organization)
use Enlivy\User;              // Global user account
use Enlivy\Organization;       // Organization
use Enlivy\AiAgent;           // AI Agent

// Organization-scoped resources (belong to an organization)
use Enlivy\Organization\Invoice;
use Enlivy\Organization\Prospect;
use Enlivy\Organization\User;  // Organization User (customer/employee)
use Enlivy\Organization\Contract;
```

When working with typed returns:
```php
// The SDK returns properly typed objects
$invoices = $client->invoices->list(); // Returns Collection<Invoice>
$invoice = $client->invoices->retrieve('org_inv_xxx'); // Returns Enlivy\Organization\Invoice

// You can type-hint in your code
use Enlivy\Organization\Invoice;

function processInvoice(Invoice $invoice): void {
    echo $invoice->number;
}
```

## Key Concepts

### OrganizationUser vs Prospect

| Entity | Purpose | Can Receive Invoices? |
|--------|---------|----------------------|
| **OrganizationUser** | Actual customer/client | Yes (with proper role) |
| **Prospect** | Sales lead in pipeline | No - link to user first |

### Invoice Types

| Source | Direction | Use Case |
|--------|-----------|----------|
| `internal` + `outbound` | Invoice you create and send to customer |
| `internal` + `inbound` | Invoice you create that customer owes you |
| `external` + `outbound` | Uploaded invoice you sent |
| `external` + `inbound` | Uploaded invoice you received |

## Error Handling

```php
use Enlivy\Exception\ApiException;
use Enlivy\Exception\ValidationException;
use Enlivy\Exception\AuthenticationException;
use Enlivy\Exception\NotFoundException;

try {
    $invoice = $client->invoices->retrieve('org_inv_nonexistent');
} catch (NotFoundException $e) {
    echo "Invoice not found: " . $e->getMessage();
} catch (ValidationException $e) {
    echo "Validation failed: " . $e->getMessage();
    print_r($e->getErrors()); // Field-level errors
} catch (AuthenticationException $e) {
    echo "Authentication failed - check your API key";
} catch (ApiException $e) {
    echo "API error: " . $e->getMessage();
}
```

## Support

- **Documentation**: https://docs.enlivy.com
- **API Reference**: https://docs.enlivy.com/api
- **Issues**: https://github.com/enlivy/enlivy-php/issues
