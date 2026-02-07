# Using Filters on List Endpoints

All `list()` endpoints support filtering via query parameters. The SDK validates filter names client-side before sending the request, catching typos and unsupported parameters early.

## Basic Usage

```php
// Filter invoices by status and direction
$invoices = $client->invoices->list([
    'status' => 'draft',
    'direction' => 'outbound',
    'per_page' => 25,
]);

// Date range filtering
$invoices = $client->invoices->list([
    'created_at_from' => '2024-01-01T00:00:00Z',
    'created_at_to' => '2024-12-31T23:59:59Z',
]);

// Combine with includes
$prospects = $client->prospects->list([
    'source_type' => 'inbound',
    'include' => ['organization_prospect_status', 'assigned_organization_user'],
    'per_page' => 10,
]);
```

## Global Filters

These filters are available on **every** list endpoint:

| Filter | Type | Description |
|--------|------|-------------|
| `q` | string (min: 3) | Full-text search |
| `q_in` | array | Restrict search to specific fields |
| `ids` | string (comma-separated) | Filter by specific IDs (max 30). Rejected when `q` is used |
| `order_by` | string | Sort by attribute (default: `created_at`) |
| `order` | string (`asc`\|`desc`) | Sort direction (default: `desc`) |
| `page` | integer | Pagination page number |
| `per_page` | integer (max: 50) | Items per page (default: 30) |
| `deleted` | integer (`-1`\|`0`\|`1`) | `-1` = include deleted, `0` = active only, `1` = deleted only |
| `tag_ids` | string (comma-separated) | Filter by tag IDs |

Global filters are **not** listed in each service's `AVAILABLE_FILTERS` constant — they are always accepted automatically.

```php
// Search and pagination
$results = $client->invoices->list([
    'q' => 'Acme Corp',
    'per_page' => 10,
    'page' => 2,
    'order_by' => 'issued_at',
    'order' => 'desc',
]);

// Filter by IDs
$specific = $client->invoices->list([
    'ids' => 'org_inv_abc,org_inv_def,org_inv_ghi',
]);

// Include soft-deleted records
$withDeleted = $client->invoices->list([
    'deleted' => -1,
]);
```

## Validation

The SDK validates filter names before sending the request. Unknown filters throw an `InvalidArgumentException`:

```php
use Enlivy\Exception\InvalidArgumentException;

try {
    $client->invoices->list([
        'nonexistent_filter' => 'value',
    ]);
} catch (InvalidArgumentException $e) {
    // "Unknown filter(s): nonexistent_filter. Available filters for this resource: direction, status, ... (plus global filters: q, q_in, ids, ...)"
    echo $e->getMessage();
}
```

> **Note:** The SDK validates filter **names** only, not filter **values**. Value validation (e.g., checking that `status` is a valid enum) is done by the API and returns a 422 error.

## Referencing Available Filters

Each service exposes its valid filters as constants:

```php
// Resource-specific filters
$filters = $client->invoices::AVAILABLE_FILTERS;
// ['direction', 'status', 'is_downloadable', 'is_tax_charged', 'paid_at_from', ...]

// Global filters (same on every service)
use Enlivy\Service\Concern\HasFilters;
$globals = HasFilters::GLOBAL_FILTERS;
// ['q', 'q_in', 'ids', 'order_by', 'order', 'page', 'per_page', 'deleted', 'tag_ids']
```

## Date Range Filters

Date range filters always come in pairs (`_from` / `_to`). The API requires:
- Both values when either is provided (`required_with`)
- `_from` must be before or equal to `_to`
- Format: ISO 8601 datetime (`Y-m-d\TH:i:s\Z`) or date (`Y-m-d`) depending on the resource

```php
// Both from and to are required together
$invoices = $client->invoices->list([
    'issued_at_from' => '2024-06-01T00:00:00Z',
    'issued_at_to' => '2024-06-30T23:59:59Z',
]);
```

## Available Filters by Service

### Invoicing

| Service | Filters |
|---------|---------|
| `invoices` | `direction` (inbound\|outbound), `status` (approval_required\|draft\|scheduled\|pending\|sent_email\|sent_physical\|payment_partial\|paid\|solved\|overdue\|cancelled), `is_downloadable` (bool), `is_tax_charged` (bool), `paid_at_from/to`, `issued_at_from/to`, `created_at_from/to`, `updated_at_from/to` |
| `receipts` | `direction` (inbound\|outbound), `status` (same as invoices), `organization_invoice_id` (string[]), `paid_at_from/to`, `issued_at_from/to`, `created_at_from/to`, `updated_at_from/to` |
| `invoicePrefixes` | `type` (standard\|reversal\|proforma) |
| `invoiceNotificationLogs` | `organization_invoice_id` |
| `invoiceNetworkExchanges` | `organization_invoice_id`, `invoice_state` (any\|attached\|unattached) |

### CRM

| Service | Filters |
|---------|---------|
| `prospects` | `organization_prospect_status_id`, `assigned_organization_user_id`, `source_type` (inbound\|outbound), `email`, `state_qualified_at_from/to`, `state_disqualified_at_from/to`, `state_won_at_from/to`, `state_lost_at_from/to`, `created_at_from/to`, `updated_at_from/to` |

### Contracts

| Service | Filters |
|---------|---------|
| `contracts` | `organization_contract_status_id`, `organization_receiver_user_id`, `organization_sender_user_id`, `organization_user_id`, `parent_organization_contract_id`, `category` (core\|amendment\|addenda\|supplement), `locale` (en\|ro\|de\|fr\|nl\|da), `source` (uploaded\|internal), `direction` (inbound\|outbound), `issued_at_from/to`, `ends_at_from/to`, `created_at_from/to`, `updated_at_from/to` |

### Banking

| Service | Filters |
|---------|---------|
| `bankTransactions` | `state` (backlog\|classified\|connected\|connected_partially\|danger\|trashed), `direction` (inbound\|outbound), `connection_entity_type` (invoice\|receipt\|bank_transaction\|user\|payslip), `connection_entity_id`, `created_at_from/to`, `updated_at_from/to` |

### Billing & Offers

| Service | Filters |
|---------|---------|
| `billingSchedules` | `status` (pending\|active\|cancelled), `type` (payment_plan\|subscription), `direction` (inbound\|outbound), `organization_sender_user_id`, `organization_receiver_user_id`, `organization_contract_id`, `organization_bank_account_id`, `starts_at_from/to`, `ends_at_from/to`, `created_at_from/to`, `updated_at_from/to` |
| `offers` | `is_public` (bool), `is_active` (bool), `currency` (3-char code), `organization_project_id`, `only_available` (bool) |
| `proposals` | `status` (draft\|sent\|viewed\|accepted\|rejected\|expired), `currency` (3-char code), `organization_project_id`, `organization_offer_id`, `organization_prospect_id`, `organization_receiver_user_id` |

### Payroll

| Service | Filters |
|---------|---------|
| `payslips` | `status` (pending\|approval_required\|rejected\|approved\|paid), `paid_at_from/to`, `issued_at_from/to`, `created_at_from/to`, `updated_at_from/to` |

### Reports

| Service | Filters |
|---------|---------|
| `reports` | `reported_at_from/to` |

### Content

| Service | Filters |
|---------|---------|
| `guidelines` | `organization_project_id`, `created_at_from/to`, `updated_at_from/to` |
| `playbooks` | `parent_organization_playbook_id`, `organization_project_id`, `created_at_from/to`, `updated_at_from/to` |
| `reusableContent` | `scope`, `entity_type` (contract\|playbook), `created_at_from/to`, `updated_at_from/to` |

### Projects

| Service | Filters |
|---------|---------|
| `projects` | `created_at_from/to`, `updated_at_from/to` |
| `projectMembers` | `member_role` (team_member\|contractor\|client\|prospect) |

### Users

| Service | Filters |
|---------|---------|
| `organizationUsers` | `created_at_from/to`, `updated_at_from/to` |

### Services with Global Filters Only

The following services accept only global filters (q, ids, page, per_page, order_by, order, deleted, tag_ids):

`bankAccounts`, `bankTransactionCostTypes`, `contractPrefixes`, `contractSignatures`, `contractStatuses`, `files`, `products`, `payslipSchemas`, `reportSchemas`, `reportSchemaFields`, `receiptPrefixes`, `tags`, `tasks`, `taskStatuses`, `userAddresses`, `userRoles`, `notifications`, `webhooks`, `prospectStatuses`, `prospectActivities`, `apiCredentials`, `resourceBundles`, `taxClasses`, `taxRates`, `taxTypes`, `taxFilingJurisdictions`, `organizations`
