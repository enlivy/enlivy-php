# Using Includes (Eager Loading)

Most Enlivy API endpoints support the `include` parameter to eager-load related resources in a single request. The SDK validates includes client-side before sending the request.

## Basic Usage

```php
// Array format (recommended — IDE autocomplete via constants)
$invoice = $client->invoices->retrieve('org_inv_xxx', [
    'include' => ['sender_user', 'receiver_user', 'line_items'],
]);

// Comma-separated string format
$invoice = $client->invoices->retrieve('org_inv_xxx', [
    'include' => 'sender_user,receiver_user,line_items',
]);

// Works on list, retrieve, create, update, and delete
$invoices = $client->invoices->list([
    'include' => ['sender_user', 'receiver_user'],
    'per_page' => 25,
]);
```

## Validation

The SDK validates includes before sending the request. Invalid includes throw an `InvalidArgumentException`:

```php
use Enlivy\Exception\InvalidArgumentException;

try {
    $client->invoices->list([
        'include' => ['nonexistent_relation'],
    ]);
} catch (InvalidArgumentException $e) {
    // "Invalid include(s): nonexistent_relation. Available includes for this resource: bank_account, invoice_prefix, ..."
    echo $e->getMessage();
}
```

## Referencing Available Includes

Each service exposes its valid includes as a constant:

```php
// Check what includes are available
$available = $client->invoices::AVAILABLE_INCLUDES;
// ['bank_account', 'invoice_prefix', 'sender_user', 'receiver_user', ...]
```

## Virtual Includes

Some resources support virtual (dot-notation) includes that load nested relationships:

```php
// Reports: include schema AND its fields in one request
$report = $client->reports->retrieve('org_rep_xxx', [
    'include' => ['report_schema', 'report_schema.report_schema_fields'],
]);

// Playbooks: include procedure files
$playbook = $client->playbooks->retrieve('org_play_xxx', [
    'include' => ['procedure_process_steps_files', 'procedure_files'],
]);
```

## Available Includes by Service

### Invoicing

| Service | Includes |
|---------|----------|
| `invoices` | `bank_account`, `invoice_prefix`, `sender_user`, `receiver_user`, `receiver_user_address`, `line_items`, `receipts`, `deleted_by_user`, `party_locales`, `tag_ids`, `taxes`, `last_peppol_exchange`, `contract`, `charge_logs`, `latest_charge_log`, `reversal_invoices`, `parent_invoice` |
| `invoices` (event trails) | `changes`, `actor_organization_user`, `charge_log` |
| `invoicePrefixes` | `organization`, `deleted_by_user`, `custom_logo` |
| `invoiceNetworkExchanges` | `organization`, `parsed_data`, `tax_mapping`¹, `recording_suggestions`¹, `invoice`, `tag_ids` |
| `invoiceNotificationLogs` | `deleted_by_user`, `organization` |
| `proposalNotificationLogs` | `deleted_by_user`, `organization`, `proposal` |
| `invoiceScheduledReminders` | _(none — rows are projected, not stored)_ |
| `receipts` | `organization`, `invoice`, `bank_account`, `sender_user`, `receiver_user`, `deleted_by_user`, `tag_ids`, `taxes`, `contract` |
| `receipts` (event trails) | `changes`, `actor_organization_user`, `charge_log` |
| `receiptPrefixes` | `organization`, `receipts`, `deleted_by_user` |

¹ Retrieve-only: the list endpoint rejects these two with a `422`.

### CRM

| Service | Includes |
|---------|----------|
| `prospects` | `organization`, `organization_prospect_stage`, `linked_organization_user`, `assigned_organization_user`, `assigned_organization_project`, `source_referrer_organization_user`, `created_by_user`, `deleted_by_user`, `proposals` |
| `prospectActivities` | `organization`, `organization_prospect`, `performed_by_organization_user`, `organization_report`, `organization_file`, `organization_prospect_stage_path`, `created_by_user`, `deleted_by_user` |
| `prospectStages` | `organization`, `deleted_by_user`, `paths`, `pipeline` |
| `prospectPipelines` | `organization`, `deleted_by_user`, `stages` |

### Contracts

| Service | Includes |
|---------|----------|
| `contracts` | `organization`, `parent_contract`, `sender_user`, `receiver_user`, `file`, `contract_status`, `deleted_by_user`, `contract_chapters`, `contract_parties`, `contract_prefix` |
| `contractSignatures` | `deleted_by_user`, `organization`, `organization_contract`, `evidence_authentication`, `evidence_consent`, `evidence_signature_biometrics`, `sign_session_url` |
| `contractStatuses` | `organization`, `deleted_by_user` |
| `contractPrefixes` | `organization`, `deleted_by_user` |

### Users & Roles

| Service | Includes |
|---------|----------|
| `organizationUsers` | `organization`, `user_role`, `deleted_by_user`, `tag_ids` |
| `userRoles` | `organization`, `user_role`, `deleted_by_user`, `tag_ids` |
| `userAddresses` | `organization`, `organization_user`, `deleted_by_user` |

### Projects

| Service | Includes |
|---------|----------|
| `projects` | `deleted_by_user`, `organization`, `tag_ids`, `project_resource_bundles`, `resource_bundles` |
| `projectMembers` | `deleted_by_user`, `organization`, `organization_project`, `organization_user` |

### Products & Banking

| Service | Includes |
|---------|----------|
| `products` | `organization`, `deleted_by_user`, `tag_ids`, `tax_class` |
| `bankAccounts` | `organization`, `deleted_by_user`, `tag_ids`, `bank_account_data_account_detail` |
| `bankTransactions` | `organization`, `bank_account`, `connection_entities`, `cost_type`, `deleted_by_user`, `tag_ids` |
| `bankTransactionCostTypes` | `organization`, `deleted_by_user`, `tag_ids` |
| `billingSchedules` | `sender_user`, `receiver_user`, `contract`, `billing_package`, `subscription_term`, `deleted_by_user`, `payments`, `phases` |
| `billingSchedules` (event trails) | `changes`, `actor_organization_user`, `charge_log` |

### Tax

| Service | Includes |
|---------|----------|
| `taxClasses` | `organization`, `tax_rates_overview` |
| `taxRates` | `organization`, `organization_tax_class`, `locations` |
| `taxTypes` | `organization` |
| `taxFilingJurisdictions` | `organization` |

### Support

| Service | Includes |
|---------|----------|
| `helpdeskConversations` | `organization`, `inbox`, `organization_project`, `visitor`, `assigned_teammate`, `contact_organization_user`, `contact_organization_prospect`, `messages`, `attachments`, `participants`, `reads`, `merged_into`, `continued_from`, `tag_ids`, `deleted_by_user` |
| `helpdeskConversationMessages` | `organization`, `conversation`, `author_organization_user`, `author_organization_prospect`, `attachments`, `deliveries` |
| `helpdeskConversationAttachments` | `organization`, `conversation`, `message`, `visitor`, `uploaded_by_user` |
| `helpdeskConversationParticipants` | `organization`, `conversation`, `organization_user`, `organization_prospect` |
| `helpdeskInboxes` | `organization`, `organization_project`, `owner_teammate`, `default_teammate`, `organization_api_credential`, `branding_logo_file`, `branding_icon_file`, `deleted_by_user` |
| `helpdeskTeammates` | `organization`, `organization_user`, `owned_inboxes` |
| `helpdeskSettings` | `organization`, `branding_logo_file`, `branding_icon_file` |
| `helpdeskInboundEmails` | `organization`, `inbox`, `organization_api_credential`, `conversation`, `message`, `content` |
| `helpdeskInboundEmailRules` | `organization`, `inbox`, `deleted_by_user` |
| `helpdeskProactiveMessages` | `organization`, `inbox`, `sender_teammate`, `deleted_by_user` |
| `helpdeskVisitors` | `organization`, `organization_user`, `organization_prospect`, `events` |
| `bankAccountStatements` | `organization`, `bank_account`, `uploaded_by_user`, `deleted_by_user` |
| `projectPermissionPipelines` | `organization`, `organization_project`, `organization_user`, `organization_prospect_pipeline` |

On the portal lane, a customer's conversation offers only `messages`, and a message always carries
its `attachments`.

`helpdeskInboundEmails` treats `content` as an include on purpose: the raw body of a message is
withheld until it is asked for by name.

### Payroll & Reports

| Service | Includes |
|---------|----------|
| `payslips` | `deleted_by_user`, `organization`, `organization_employment`, `lines`, `receiver_user`, `sender_user` |
| `employments` | `organization`, `organization_user`, `organization_contract`, `organization_employment_jurisdictions`, `organization_employment_agreements`, `deleted_by_user` |
| `workingTimeTerms` | `organization`, `organization_employment`, `deleted_by_user` |
| `workingTimeDays` | `organization`, `organization_employment`, `organization_working_time_day_breaks` |
| `reports` | `organization`, `report_schema`, `organization_user`, `organization_user_role`, `deleted_by_user`, `organization_project`, `report_schema.report_schema_fields` |
| `reportSchemas` | `organization`, `report_schema_fields`, `deleted_by_user` |
| `resourceBundles` | `organization` |
| `reportSchemaFields` | `report_schema`, `deleted_by_user` |

### Content & Tasks

| Service | Includes |
|---------|----------|
| `files` | `organization`, `deleted_by_user`, `uploaded_by_user`, `tag_ids` |
| `guidelines` | `deleted_by_user`, `organization`, `organization_owner_user`, `organization_project`, `tag_ids` |
| `playbooks` | `organization`, `procedure_organization_owner_user`, `deleted_by_user`, `organization_project`, `parent_organization_playbook`, `tag_ids` |
| `playbooks` | `organization`, `procedure_organization_owner_user`, `deleted_by_user`, `organization_project`, `parent_organization_playbook`, `tag_ids`, `procedure_process_steps_files`, `procedure_files` |
| `tasks` | `assigned_by_organization_user`, `assigned_to_organization_user`, `completed_by_organization_user`, `deleted_by_user`, `organization`, `parent_organization_task`, `organization_project`, `organization_task_status`, `organization_report_schema`, `organization_report` |
| `taskStatuses` | `organization`, `deleted_by_user` |

### Other

| Service | Includes |
|---------|----------|
| `tags` | `organization`, `deleted_by_user` |
| `blockedIdentifiers` | `organization` |
| `eventDestinations` | `organization`, `deleted_by_user`, `event_subscriptions`, `event_deliveries` |
| `billingPackages` | `organization`, `project`, `groups`, `payment_plans`, `contract_templates`, `created_by_user`, `deleted_by_user`, `expired_by_user` |
| `proposals` | `organization`, `project`, `billing_package`, `billing_package_payment_plan`, `subscription_term`, `payments`, `proposal_contracts`, `billing_schedule`, `invoice`, `proforma_invoice`, `prospect`, `receiver_user`, `sender_user`, `created_by_user`, `deleted_by_user`, `expired_by_user`, `stage_detail` |
| `notifications` | `organization`, `sent_to_organization_user` |

### Global Services

| Service | Includes |
|---------|----------|
| `organizations` | `schema`, `user_abilities`, `settings`, `deleted_by_user`, `remaining_credits`, `credits`, `branding_icon`, `branding_logo`, `membership_features` |
| `exportData` | (no includes) |
| `users` (global) | `organizations` |
| `aiAgents` | `deleted_by_user` |
| `oauthClients` | `user` |
| `invitationCodes` | `deleted_by_user` |
