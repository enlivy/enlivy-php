# Reports

Create dynamic reports with custom schemas and fields. Reports are submitted by organization users using predefined schema structures.

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

1. Create a report schema defining the structure
2. Add fields to the schema
3. Users create reports using the schema, filling in the `report_map`

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
    'name' => 'Weekly Sales Report',
    'description' => 'Track weekly sales performance',
    'is_active' => true,
]);

echo "Schema created: {$schema->id}\n";
```

### List Schemas

```php
<?php

$schemas = $client->reportSchemas->list([
    'include' => ['fields'],
]);

foreach ($schemas as $schema) {
    echo "{$schema->name}\n";
    foreach ($schema->fields ?? [] as $field) {
        echo "  - {$field->name} ({$field->type})\n";
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

### Report with Locale and Timestamp

```php
<?php

$report = $client->reports->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'organization_user_id' => 'org_user_xxx',

    // Optional metadata
    'locale' => 'en',
    'reported_at' => '2026-02-08 17:30:00', // When the report period ends

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
    'include' => ['schema', 'organization_user'],
]);

foreach ($reports as $report) {
    echo "Report by {$report->organization_user->first_name}\n";
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
    if ($report->reported_at) {
        echo "  Reported: {$report->reported_at}\n";
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

## Retrieving a Report

```php
<?php

$report = $client->reports->retrieve('org_report_xxx', [
    'include' => ['schema', 'organization_user', 'organization_project'],
]);

echo "Report: {$report->id}\n";
echo "Schema: {$report->schema->name}\n";
echo "Submitted by: {$report->organization_user->first_name}\n";

if ($report->organization_project) {
    echo "Project: {$report->organization_project->title_lang_map['en']}\n";
}

echo "Data:\n";
foreach ($report->report_map as $field => $value) {
    echo "  {$field}: {$value}\n";
}
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
| `reported_at` | datetime | No | Reporting period timestamp |

### Include Options

| Include | Description |
|---------|-------------|
| `schema` | Report schema details |
| `organization_user` | Submitting user details |
| `organization_project` | Linked project details |
| `organization` | Organization details |
| `deleted_by_user` | User who deleted |

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
        'name' => 'Monthly KPI Report',
        'description' => 'Track monthly key performance indicators',
        'is_active' => true,
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
    $users = $client->organizationUsers->list(['per_page' => 1]);
    $user = $users->data[0];

    // 4. Create monthly report
    $report = $client->reports->create([
        'organization_report_schema_id' => $schema->id,
        'organization_user_id' => $user->id,
        'reported_at' => '2026-01-31 23:59:59',
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
        'include' => ['schema', 'organization_user'],
    ]);

    echo "\n--- Report Summary ---\n";
    echo "Schema: {$report->schema->name}\n";
    echo "Submitted by: {$report->organization_user->first_name}\n";
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
- [Organization Users](organization-users.md) - Report submitters
