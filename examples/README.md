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

// Option 1: Global configuration
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

// Create an invoice
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

echo "Invoice created: {$invoice->id}\n";
```

## Documentation Structure

```
examples/
├── README.md                 # This file
├── authentication.md         # API keys, OAuth client credentials
├── oauth.md                  # OAuth server for third-party apps
├── ai-agents.md              # AI-powered features
├── integrations.md           # Stripe, ANAF, and other integrations
│
└── organization/             # Organization-scoped resources
    ├── users.md              # Organization users (customers/employees)
    ├── invoices.md           # Creating and sending invoices
    ├── receipts.md           # Receipt management
    ├── contracts.md          # Contract management and e-signatures
    ├── proposals.md          # Sales proposals
    ├── prospects.md          # Sales pipeline and CRM
    ├── products.md           # Product catalog
    ├── taxes.md              # Tax configuration
    ├── projects.md           # Projects and team management
    ├── bank-accounts.md      # Bank accounts and transactions
    ├── reports.md            # Dynamic reports and schemas
    ├── files.md              # File uploads and attachments
    ├── webhooks.md           # Webhook endpoints
    └── customer-portal.md    # External user access
```

## Documentation by Topic

### Getting Started
- [Authentication](authentication.md) - API keys, OAuth, configuration

### Global Services
- [OAuth Server](oauth.md) - OAuth server for third-party apps
- [AI Agents](ai-agents.md) - AI-powered features
- [Integrations](integrations.md) - Stripe, ANAF, and other integrations

### Organization Resources

#### Customer Management
- [Organization Users](organization/users.md) - Creating and managing customers/clients
- [Prospects](organization/prospects.md) - Sales pipeline and CRM

#### Billing & Invoicing
- [Invoices](organization/invoices.md) - Creating and sending invoices
- [Receipts](organization/receipts.md) - Receipt management
- [Products](organization/products.md) - Product catalog
- [Taxes](organization/taxes.md) - Tax configuration

#### Contracts & Proposals
- [Contracts](organization/contracts.md) - Contract management and e-signatures
- [Proposals](organization/proposals.md) - Sales proposals from offer templates

#### Project Management
- [Projects](organization/projects.md) - Projects and team management

#### Banking
- [Bank Accounts](organization/bank-accounts.md) - Bank accounts and transactions

#### Reports & Files
- [Reports](organization/reports.md) - Dynamic reports and schemas
- [Files](organization/files.md) - File uploads and attachments

#### Webhooks & Portal
- [Webhooks](organization/webhooks.md) - Webhook endpoints and event handling
- [Customer Portal](organization/customer-portal.md) - External user access

## Namespace Structure

The SDK mirrors this documentation structure in code:

```php
// Global resources (not organization-scoped)
use Enlivy\User;              // Global user account
use Enlivy\Organization;       // Organization
use Enlivy\AiAgent;           // AI Agent
use Enlivy\OAuthClient;       // OAuth Client

// Organization-scoped resources
use Enlivy\Organization\Invoice;
use Enlivy\Organization\Prospect;
use Enlivy\Organization\User;      // Organization User (customer/employee)
use Enlivy\Organization\Contract;
use Enlivy\Organization\Product;
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

### Multilingual Fields

Most text fields in Enlivy use `_lang_map` for multilingual support:

```php
// Multilingual fields use language keys
'name_lang_map' => [
    'en' => 'Consulting Services',
    'ro' => 'Servicii de Consultanta',
],
'description_lang_map' => [
    'en' => 'Professional consulting services',
],
```

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
| `oac_` | OAuth Client |
| `ai_agent_` | AI Agent |

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
