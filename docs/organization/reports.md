# Reports

Create dynamic reports with custom schemas and fields. Reports are filed by organization users using predefined schema structures. A schema's
`kind` decides what a report covers, who it is for, what it may link to and whether that person acknowledges it.

## Key Concepts

### Report Structure

```
Report Schema (defines structure)
    |
    +-- Schema Fields (define inputs)
    |
Report (instance with filled data)
    +-- report_map (field values)
```

### Workflow

1. Create a report schema of a kind, defining the structure
2. Add fields to the schema
3. Users file reports using the schema, filling in the `report_map`
4. The person a report is for acknowledges it, when the schema asks for that

### Kinds

| Kind | Covers a period | Recipient | Acknowledgement (default first) | May link to |
|------|-----------------|-----------|----------------------------------|-------------|
| `daily_log` | no | none | `none` | task |
| `work_report` | yes | required | `optional`, `none`, `required` | invoice, contract, billing schedule, task |
| `meeting_notes` | no | optional | `none`, `optional`, `required` | prospect, contract, proposal, task |
| `acceptance` | no | required | `required` | contract, invoice, proposal |
| `feedback` | no | none | `none` | task |
| `general` | no | optional | `none`, `optional`, `required` | invoice, contract, receipt, proposal, billing schedule, prospect, task |

The values are `Enums\ReportSchema\Kinds` and `Enums\ReportSchema\Requirements`. A schema whose
acknowledgement is `required` always needs a recipient. `$client->frontend->all()` lists the same
under `report.schema_kinds`.

## Report Schemas

Define the structure of reports.

### Create Schema

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$schema = $client->reportSchemas->create([
    'kind' => 'work_report',
    'acknowledgement' => 'required',
    'title' => 'Weekly Sales Report',
    'title_lang_map' => ['en' => 'Weekly Sales Report'],
    'description_lang_map' => ['en' => 'Track weekly sales performance'],
    'frequency' => 'weekly',
]);

echo "Schema created: {$schema->id}\n";
```

`acknowledgement` defaults to the kind's first option and must be one the kind allows. Once any report
was filed under a schema, its kind no longer changes: `kind_locked` reads true.

### List Schemas

```php
<?php

$schemas = $client->reportSchemas->list([
    'kind' => 'work_report',
    'include' => ['report_schema_fields'],
]);

foreach ($schemas as $schema) {
    echo "{$schema->title} ({$schema->kind})\n";
    foreach ($schema->report_schema_fields->data ?? [] as $field) {
        echo "  - {$field->title} ({$field->type})\n";
    }
}
```

## Schema Fields

Define fields within a schema.

### Create Fields

```php
<?php

// Text field
$client->reportSchemaFields->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'name' => 'summary',
    'label' => 'Summary',
    'type' => 'text',
    'is_required' => true,
    'order' => 1,
]);

// Number field
$client->reportSchemaFields->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'name' => 'deals_closed',
    'label' => 'Deals Closed',
    'type' => 'number',
    'is_required' => true,
    'order' => 2,
]);

// Currency field
$client->reportSchemaFields->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'name' => 'revenue',
    'label' => 'Total Revenue',
    'type' => 'currency',
    'is_required' => true,
    'order' => 3,
    'options' => ['currency' => 'EUR'],
]);

// Date field
$client->reportSchemaFields->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'name' => 'week_ending',
    'label' => 'Week Ending',
    'type' => 'date',
    'is_required' => true,
    'order' => 4,
]);

// Select field
$client->reportSchemaFields->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'name' => 'performance',
    'label' => 'Performance Rating',
    'type' => 'select',
    'is_required' => false,
    'order' => 5,
    'options' => [
        'choices' => ['Excellent', 'Good', 'Average', 'Below Average'],
    ],
]);
```

### Field Types

| Type | Description |
|------|-------------|
| `text` | Single line text |
| `textarea` | Multi-line text |
| `number` | Numeric value |
| `currency` | Money amount |
| `date` | Date picker |
| `datetime` | Date and time |
| `select` | Dropdown selection |
| `multiselect` | Multiple selection |
| `checkbox` | Boolean true/false |
| `file` | File upload |

## Creating Reports

### Basic Report

```php
<?php

$report = $client->reports->create([
    // Required fields
    'organization_report_schema_id' => 'org_schema_xxx',
    'organization_user_id' => 'org_user_xxx', // Who submitted the report

    // Report data (matches schema fields)
    'report_map' => [
        'summary' => 'Strong week with 3 major deals closed.',
        'deals_closed' => 5,
        'revenue' => 45000.00,
        'week_ending' => '2026-02-08',
        'performance' => 'Excellent',
    ],
]);

echo "Report created: {$report->id}\n";
```

### Report with Project Assignment

```php
<?php

$report = $client->reports->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'organization_user_id' => 'org_user_xxx',
    'organization_project_id' => 'org_proj_xxx', // Link to project

    'report_map' => [
        'summary' => 'Project milestone completed ahead of schedule.',
        'deals_closed' => 2,
        'revenue' => 30000.00,
        'week_ending' => '2026-02-08',
    ],
]);
```

### Work Report for a Period and a Recipient

```php
<?php

$report = $client->reports->create([
    'organization_report_schema_id' => 'org_repschema_xxx',
    'organization_user_id' => 'org_user_xxx',
    'period_from' => '2026-10-01',
    'period_to' => '2026-10-15',
    'recipient_organization_user_id' => 'org_user_client_xxx',
    'organization_task_id' => 'org_task_xxx',
    'links' => [
        ['type' => 'invoice', 'id' => 'org_inv_xxx'],
        ['type' => 'contract', 'id' => 'org_cont_xxx'],
    ],
    'report_map' => [
        'summary' => 'Phase one delivered.',
    ],
]);

echo "{$report->status}, {$report->worked_minutes} minutes\n";
```

- `period_from` and `period_to` are required for a kind that covers a period and refused otherwise;
  the same holds for `recipient_organization_user_id` by the table above.
- `links` takes up to 20 records the kind may link to; sending it replaces the list. A report filed
  for a task without `links` takes the task's links. Linking needs the right to view each record.
- `status` is `submitted` unless you send `draft`. Submitting a report filed for a task that asked
  for this schema (`requested_report_schema_id`) completes the task.
- `worked_minutes` is what the report's timesheet answers add up to, null without a timesheet.

### Report with Locale and Timestamp

```php
<?php

$report = $client->reports->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'organization_user_id' => 'org_user_xxx',

    // Optional metadata
    'locale' => 'en',
    'report_date' => '2026-02-08', // Date of the report (Y-m-d)

    'report_map' => [
        'summary' => 'Weekly performance summary.',
        'deals_closed' => 4,
        'revenue' => 38000.00,
        'week_ending' => '2026-02-08',
        'performance' => 'Good',
    ],
]);
```

## Listing Reports

### Basic List

```php
<?php

$reports = $client->reports->list();

foreach ($reports as $report) {
    echo "Report: {$report->id}\n";
    echo "  Schema: {$report->organization_report_schema_id}\n";
    echo "  Submitted by: {$report->organization_user_id}\n";
}
```

### Filter by Schema

```php
<?php

$reports = $client->reports->list([
    'organization_report_schema_id' => 'org_schema_xxx',
    'include' => ['report_schema', 'organization_user'],
]);

foreach ($reports as $report) {
    echo "Report by {$report->organization_user?->data->first_name}\n";
    echo "  Revenue: {$report->report_map['revenue']}\n";
    echo "  Deals: {$report->report_map['deals_closed']}\n";
}
```

### Filter by User

```php
<?php

$reports = $client->reports->list([
    'organization_user_id' => 'org_user_xxx',
]);

foreach ($reports as $report) {
    echo "Report: {$report->id}\n";
    if ($report->report_date) {
        echo "  Reported: {$report->report_date}\n";
    }
}
```

### Filter by Project

```php
<?php

$reports = $client->reports->list([
    'organization_project_id' => 'org_proj_xxx',
]);
```

### Lifecycle and Links

```php
<?php

$waiting = $client->reports->list([
    'awaiting_acknowledgement' => true,
    'recipient_organization_user_id' => 'org_user_client_xxx',
]);

$aboutInvoice = $client->reports->list([
    'linked_type' => 'invoice',
    'linked_id' => 'org_inv_xxx',
    'include' => ['links'],
]);
```

`kind`, `status`, `organization_task_id` and `recipient_organization_user_id` filter too.
`linked_type` and `linked_id` go together.

## Retrieving a Report

```php
<?php

$report = $client->reports->retrieve('org_report_xxx', [
    'include' => ['report_schema', 'organization_user', 'organization_project'],
]);

echo "Report: {$report->id}\n";
echo "Schema: {$report->report_schema?->data->title}\n";
echo "Submitted by: {$report->organization_user?->data->first_name}\n";

if ($project = $report->organization_project?->data) {
    echo "Project: {$project->title_lang_map['en']}\n";
}

echo "Data:\n";
foreach ($report->report_map as $field => $value) {
    echo "  {$field}: {$value}\n";
}
```

## Status and Acknowledgement

| Field | Meaning |
|-------|---------|
| `status` | `draft` (the author's alone), `submitted` (final for its author), `acknowledged` (final for everyone) |
| `acknowledgement` | The schema's: `none`, `optional` or `required` |
| `awaiting_acknowledgement` | Submitted for a recipient under a schema that asks for acknowledgement, and not acknowledged yet |
| `submitted_at`, `acknowledged_at`, `acknowledged_by_organization_user_id` | When it was submitted and acknowledged, and by whom |

Only the recipient acknowledges, from the customer portal:

```php
<?php

$report = $portal->reports->acknowledge('org_rep_xxx');
```

## Updating a Report

```php
<?php

$report = $client->reports->update('org_report_xxx', [
    'report_map' => [
        'summary' => 'Updated summary with additional notes.',
        'deals_closed' => 6, // Corrected
        'revenue' => 52000.00, // Updated
        'week_ending' => '2026-02-08',
        'performance' => 'Excellent',
    ],
]);

echo "Report updated: {$report->id}\n";
```

The back office may correct any report, acknowledged ones too, move it to another project or author,
and switch it to another schema of the same kind (send `report_map` with it). A draft is submitted by
sending `'status' => 'submitted'`. The `author_portal_access` include says whether the author can
still edit it in the portal, and until when:

```php
<?php

$report = $client->reports->retrieve('org_rep_xxx', ['include' => ['author_portal_access']]);

if ($report->author_portal_access['can_edit']) {
    echo "The author may edit until " . ($report->author_portal_access['can_edit_until'] ?? 'no end') . "\n";
}
```

`links`, `schema_snapshot` and `author_portal_access` are read directly, not through `data`.

## Deleting a Report

```php
<?php

// Soft delete
$report = $client->reports->delete('org_report_xxx');

echo "Deleted at: {$report->deleted_at}\n";
```

## Restoring a Report

```php
<?php

$report = $client->reports->restore('org_report_xxx');

echo "Restored: {$report->id}\n";
```

## Field Reference

### Report Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `organization_report_schema_id` | string | Yes | Schema ID defining report structure |
| `organization_user_id` | string | Yes | User who submitted the report |
| `report_map` | object | No | Field values (matches schema fields) |
| `organization_project_id` | string | No | Link to project |
| `locale` | string | No | Report locale |
| `report_date` | date (Y-m-d) | No | Date of the report |
| `period_from`, `period_to` | date (Y-m-d) | By kind | The period a `work_report` covers |
| `recipient_organization_user_id` | string | By kind | Who the report is for |
| `organization_task_id` | string | No | The task that asked for it |
| `links` | array | No | `type` and `id` of the records it is about |
| `status` | string | No | `draft` or `submitted` (default) |

### Include Options

| Include | Description |
|---------|-------------|
| `report_schema` | Report schema details |
| `report_schema.report_schema_fields` | The schema with its fields |
| `organization_user` | Submitting user details |
| `organization_user_role` | The author's role |
| `organization_project` | Linked project details |
| `organization_task` | The task that asked for the report |
| `recipient_organization_user` | Who the report is for |
| `acknowledged_by_organization_user` | Who acknowledged it |
| `links` | The linked records, as `type` and `id` |
| `schema_snapshot` | The schema as it stood when the report was filed |
| `author_portal_access` | Whether the author can still edit it in the portal |
| `organization` | Organization details |
| `deleted_by_user` | User who deleted |

## In the Customer Portal

A portal reader sees their own reports and others' as their [access grants](portal-access.md) allow,
plus submitted reports addressed to them. Each report carries `can_edit`, `can_edit_until` (null when
there is no end) and `can_acknowledge` for the reader. The portal list takes the back office filters
except `organization_user_id` and `organization_user_role_id`. The portal overview
(`$portal->profile->overview()`) counts `reports.awaiting_my_acknowledgement_count` and answers
`capabilities.report_filing`, whether the reader can file a report now. `awaiting_acknowledgement` alone
lists every report waiting on anyone the reader can see; add the reader as recipient for their own.

```php
<?php

$me = $portal->profile->retrieve();

$toAcknowledge = $portal->reports->list([
    'awaiting_acknowledgement' => true,
    'recipient_organization_user_id' => $me->id,
]);

$fileable = $portal->reportSchemas->list(['access_ability' => 'create']);
```

A person files a report for a task only when they are one of its assignees, and cannot link records
from the portal.

## Complete Example: KPI Reporting System

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create schema
    $schema = $client->reportSchemas->create([
        'kind' => 'general',
        'title' => 'Monthly KPI Report',
        'description_lang_map' => ['en' => 'Track monthly key performance indicators'],
        'frequency' => 'on_demand',
    ]);

    echo "Schema created: {$schema->id}\n";

    // 2. Add fields
    $fields = [
        ['name' => 'month', 'label' => 'Month', 'type' => 'date', 'order' => 1, 'is_required' => true],
        ['name' => 'new_customers', 'label' => 'New Customers', 'type' => 'number', 'order' => 2, 'is_required' => true],
        ['name' => 'revenue', 'label' => 'Revenue', 'type' => 'currency', 'order' => 3, 'is_required' => true],
        ['name' => 'churn_rate', 'label' => 'Churn Rate (%)', 'type' => 'number', 'order' => 4, 'is_required' => true],
        ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'order' => 5, 'is_required' => false],
    ];

    foreach ($fields as $field) {
        $client->reportSchemaFields->create([
            'organization_report_schema_id' => $schema->id,
            ...$field,
        ]);
    }

    echo "Added " . count($fields) . " fields\n";

    // 3. Get a user to submit the report
    $users = $client->organizationUsers->list(['limit' => 1]);
    $user = $users->data[0];

    // 4. Create monthly report
    $report = $client->reports->create([
        'organization_report_schema_id' => $schema->id,
        'organization_user_id' => $user->id,
        'report_date' => '2026-01-31',
        'report_map' => [
            'month' => '2026-01-01',
            'new_customers' => 47,
            'revenue' => 125000.00,
            'churn_rate' => 2.5,
            'notes' => 'Strong start to the year. Focus on retention.',
        ],
    ]);

    echo "Report created: {$report->id}\n";

    // 5. Retrieve and display
    $report = $client->reports->retrieve($report->id, [
        'include' => ['report_schema', 'organization_user'],
    ]);

    echo "\n--- Report Summary ---\n";
    echo "Schema: {$report->report_schema?->data->title}\n";
    echo "Submitted by: {$report->organization_user?->data->first_name}\n";
    echo "New Customers: {$report->report_map['new_customers']}\n";
    echo "Revenue: {$report->report_map['revenue']}\n";
    echo "Churn Rate: {$report->report_map['churn_rate']}%\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Projects](projects.md) - Reports within projects
- [Portal Access](portal-access.md) - Who files and reads which reports in the portal
- [Tasks](tasks.md) - Tasks that ask for a report
- [Organization Users](users.md) - Report submitters
