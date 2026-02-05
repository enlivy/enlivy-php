# Reports

Create dynamic reports with custom schemas and fields.

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

### Create Report

```php
<?php

$report = $client->reports->create([
    'organization_report_schema_id' => 'org_schema_xxx',
    'title' => 'Sales Report - Week 6',
    'data' => [
        'summary' => 'Strong week with 3 major deals closed.',
        'deals_closed' => 5,
        'revenue' => 45000.00,
        'week_ending' => '2026-02-08',
        'performance' => 'Excellent',
    ],
]);

echo "Report created: {$report->id}\n";
```

### List Reports

```php
<?php

$reports = $client->reports->list([
    'filter' => [
        'organization_report_schema_id' => 'org_schema_xxx',
    ],
    'include' => ['schema'],
]);

foreach ($reports as $report) {
    echo "{$report->title}\n";
    echo "  Revenue: {$report->data['revenue']}\n";
    echo "  Deals: {$report->data['deals_closed']}\n";
}
```

### Update Report

```php
<?php

$report = $client->reports->update('org_report_xxx', [
    'data' => [
        'summary' => 'Updated summary with additional notes.',
        'deals_closed' => 6, // Corrected
        'revenue' => 52000.00, // Updated
        'week_ending' => '2026-02-08',
        'performance' => 'Excellent',
    ],
]);
```

## Complete Example

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

// 1. Create schema
$schema = $client->reportSchemas->create([
    'name' => 'Monthly KPI Report',
    'description' => 'Track monthly key performance indicators',
]);

// 2. Add fields
$fields = [
    ['name' => 'month', 'label' => 'Month', 'type' => 'date', 'order' => 1],
    ['name' => 'new_customers', 'label' => 'New Customers', 'type' => 'number', 'order' => 2],
    ['name' => 'revenue', 'label' => 'Revenue', 'type' => 'currency', 'order' => 3],
    ['name' => 'churn_rate', 'label' => 'Churn Rate (%)', 'type' => 'number', 'order' => 4],
    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'order' => 5],
];

foreach ($fields as $field) {
    $client->reportSchemaFields->create([
        'organization_report_schema_id' => $schema->id,
        'is_required' => true,
        ...$field,
    ]);
}

// 3. Create monthly report
$report = $client->reports->create([
    'organization_report_schema_id' => $schema->id,
    'title' => 'KPI Report - January 2026',
    'data' => [
        'month' => '2026-01-01',
        'new_customers' => 47,
        'revenue' => 125000.00,
        'churn_rate' => 2.5,
        'notes' => 'Strong start to the year. Focus on retention.',
    ],
]);

echo "Report created: {$report->title}\n";
```

## Related

- [Projects](projects.md) - Reports within projects
