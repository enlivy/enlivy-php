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

**`invoices`**

| Filter | Type |
|--------|------|
| `direction` | `inbound`, `outbound` |
| `status` | `approval_required`, `draft`, `scheduled`, `pending`, `sent_email`, `sent_physical`, `payment_partial`, `paid`, `solved`, `overdue`, `cancelled` |
| `is_downloadable` | bool |
| `is_tax_charged` | bool |
| `paid_at_from`, `paid_at_to` | datetime |
| `issued_at_from`, `issued_at_to` | datetime |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

**`receipts`**

| Filter | Type |
|--------|------|
| `direction` | `inbound`, `outbound` |
| `status` | Same as invoices |
| `organization_invoice_id` | string[] |
| `paid_at_from`, `paid_at_to` | datetime |
| `issued_at_from`, `issued_at_to` | datetime |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

**`invoicePrefixes`**

| Filter | Type |
|--------|------|
| `type` | `standard`, `reversal`, `proforma` |

**`invoiceNotificationLogs`**

| Filter | Type |
|--------|------|
| `organization_invoice_id` | string |

**`invoiceNetworkExchanges`**

| Filter | Type |
|--------|------|
| `organization_invoice_id` | string |
| `invoice_state` | `any`, `attached`, `unattached` |

### CRM

**`prospects`**

| Filter | Type |
|--------|------|
| `organization_prospect_status_id` | string |
| `assigned_organization_user_id` | string |
| `source_type` | `inbound`, `outbound` |
| `email` | string |
| `state_qualified_at_from`, `state_qualified_at_to` | datetime |
| `state_disqualified_at_from`, `state_disqualified_at_to` | datetime |
| `state_won_at_from`, `state_won_at_to` | datetime |
| `state_lost_at_from`, `state_lost_at_to` | datetime |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

### Contracts

**`contracts`**

| Filter | Type |
|--------|------|
| `organization_contract_status_id` | string |
| `organization_receiver_user_id` | string |
| `organization_sender_user_id` | string |
| `organization_user_id` | string |
| `parent_organization_contract_id` | string |
| `category` | `core`, `amendment`, `addenda`, `supplement` |
| `locale` | `en`, `ro`, `de`, `fr`, `nl`, `da` |
| `source` | `uploaded`, `internal` |
| `direction` | `inbound`, `outbound` |
| `issued_at_from`, `issued_at_to` | datetime |
| `ends_at_from`, `ends_at_to` | datetime |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

### Banking

**`bankTransactions`**

| Filter | Type |
|--------|------|
| `state` | `backlog`, `classified`, `connected`, `connected_partially`, `danger`, `trashed` |
| `direction` | `inbound`, `outbound` |
| `connection_entity_type` | `invoice`, `receipt`, `bank_transaction`, `user`, `payslip` |
| `connection_entity_id` | string |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

### Billing & Offers

**`billingSchedules`**

| Filter | Type |
|--------|------|
| `status` | `pending`, `active`, `cancelled` |
| `type` | `payment_plan`, `subscription` |
| `direction` | `inbound`, `outbound` |
| `organization_sender_user_id` | string |
| `organization_receiver_user_id` | string |
| `organization_contract_id` | string |
| `organization_bank_account_id` | string |
| `starts_at_from`, `starts_at_to` | datetime |
| `ends_at_from`, `ends_at_to` | datetime |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

**`offers`**

| Filter | Type |
|--------|------|
| `is_public` | bool |
| `is_active` | bool |
| `currency` | string (3-char ISO code) |
| `organization_project_id` | string |
| `only_available` | bool |

**`proposals`**

| Filter | Type |
|--------|------|
| `status` | `draft`, `sent`, `viewed`, `accepted`, `rejected`, `expired` |
| `currency` | string (3-char ISO code) |
| `organization_project_id` | string |
| `organization_offer_id` | string |
| `organization_prospect_id` | string |
| `organization_receiver_user_id` | string |

### Payroll

**`payslips`**

| Filter | Type |
|--------|------|
| `status` | `pending`, `approval_required`, `rejected`, `approved`, `paid` |
| `paid_at_from`, `paid_at_to` | datetime |
| `issued_at_from`, `issued_at_to` | datetime |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

### Reports

**`reports`**

| Filter | Type |
|--------|------|
| `organization_report_schema_id` | string |
| `organization_user_id` | string |
| `organization_user_role_id` | string |
| `organization_project_id` | string |
| `report_date_from`, `report_date_to` | date (Y-m-d) |

### Content

**`guidelines`**

| Filter | Type |
|--------|------|
| `organization_project_id` | string |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

**`playbooks`**

| Filter | Type |
|--------|------|
| `parent_organization_playbook_id` | string |
| `organization_project_id` | string |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

**`reusableContent`**

| Filter | Type |
|--------|------|
| `scope` | string |
| `entity_type` | `contract`, `playbook` |
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

### Projects

**`projects`**

| Filter | Type |
|--------|------|
| `created_at_from`, `created_at_to` | datetime |
| `updated_at_from`, `updated_at_to` | datetime |

**`projectMembers`**

| Filter | Type |
|--------|------|
| `member_role` | `team_member`, `contractor`, `client`, `prospect` |

### Users

**`organizationUsers`**

| Filter | Type |
|--------|------|
| `can_be_invoiced` | bool |
| `can_be_invoicing` | bool |
| `can_be_payrolled` | bool |
| `can_use_backoffice` | bool |
| `email` | string |
| `organization_user_role_id` | string |

### Services with Global Filters Only

The following services accept only global filters (q, ids, page, per_page, order_by, order, deleted, tag_ids):

`bankAccounts`, `bankTransactionCostTypes`, `contractPrefixes`, `contractSignatures`, `contractStatuses`, `files`, `products`, `payslipSchemas`, `reportSchemas`, `reportSchemaFields`, `receiptPrefixes`, `tags`, `tasks`, `taskStatuses`, `userAddresses`, `userRoles`, `notifications`, `webhooks`, `prospectStatuses`, `prospectActivities`, `apiCredentials`, `resourceBundles`, `taxClasses`, `taxRates`, `taxTypes`, `taxFilingJurisdictions`, `organizations`
