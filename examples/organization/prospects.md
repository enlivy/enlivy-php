# Prospects (CRM)

Manage your sales pipeline with prospects, statuses, and activities.

## Key Concepts

### Prospect vs OrganizationUser

| Entity | Purpose | Can Be Invoiced? |
|--------|---------|------------------|
| **Prospect** | Sales lead in your pipeline | No |
| **OrganizationUser** | Actual customer in your system | Yes (with proper role) |

A prospect represents a potential deal. Once won, you typically create an OrganizationUser and link them together.

## Creating a Prospect

### Basic Prospect

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// Either first_name OR company_name is required
$prospect = $client->prospects->create([
    'title' => 'Website Redesign Project',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'company_name' => 'Acme Corporation',
]);

echo "Prospect created: {$prospect->id}\n";
echo "Title: {$prospect->title}\n";
```

### Prospect with Full Details

```php
<?php

$prospect = $client->prospects->create([
    // Title (deal name)
    'title' => 'Enterprise CRM Implementation',

    // Contact info (either first_name or company_name required)
    'first_name' => 'Sarah',
    'last_name' => 'Johnson',
    'email' => 'sarah.johnson@bigcorp.com',
    'phone_number' => '555123456',
    'phone_number_country_code' => 'US',
    'company_name' => 'BigCorp Industries',
    'country_code' => 'US',

    // Social profiles (optional array)
    'social_profiles' => [
        ['platform' => 'linkedin', 'url' => 'https://linkedin.com/in/sarah-johnson'],
        ['platform' => 'twitter', 'handle' => '@sarahjohnson'],
    ],

    // Deal info (budget is string, not numeric)
    'budget' => '75000',
    'budget_currency' => 'USD',
    'summary' => 'Looking for a complete CRM solution with custom integrations.',

    // Source tracking
    'source_type' => 'inbound', // inbound, outbound, referral, etc.
    'source_channel' => 'website',
    'source_campaign' => 'google-ads-q1',
    'source_referrer_organization_user_id' => 'org_user_referrer_xxx', // Optional referrer

    // Pipeline position
    'organization_prospect_status_id' => 'org_pros_status_qualified_xxx',

    // Assignment
    'assigned_organization_user_id' => 'org_user_sales_rep_xxx',
    'assigned_organization_project_id' => 'org_proj_xxx',

    // Link to existing customer (if converting or re-engaging)
    'linked_organization_user_id' => 'org_user_xxx',
]);

echo "Created: {$prospect->title}\n";
echo "Budget: {$prospect->budget} {$prospect->budget_currency}\n";
```

### Prospect with State Tracking

```php
<?php

// Create prospect with qualification state
$prospect = $client->prospects->create([
    'first_name' => 'Alex',
    'last_name' => 'Chen',
    'email' => 'alex@startup.io',
    'company_name' => 'Tech Startup Inc',
    'title' => 'SaaS Integration',
    'budget' => '25000',
    'budget_currency' => 'USD',

    // State timestamps (set when prospect moves through stages)
    'state_qualified_at' => '2026-02-05 10:30:00',
    // 'state_disqualified_at' => null,
    // 'state_disqualified_reason' => null,
    // 'state_won_at' => null,
    // 'state_lost_at' => null,
    // 'state_lost_reason' => null,
]);
```

## Listing Prospects

### Basic List

```php
<?php

$prospects = $client->prospects->list();

foreach ($prospects as $prospect) {
    $name = $prospect->company_name ?? "{$prospect->first_name} {$prospect->last_name}";
    echo "{$prospect->title} - {$name}\n";
}
```

### With Pagination

```php
<?php

$prospects = $client->prospects->list([
    'page' => 1,
    'per_page' => 25,
    'sort' => '-created_at', // Newest first
]);

echo "Total prospects: {$prospects->getTotalCount()}\n";
```

### With Filters

```php
<?php

// By status
$qualified = $client->prospects->list([
    'filter' => [
        'organization_prospect_status_id' => 'org_pros_status_qualified_xxx',
    ],
]);

// By assigned user
$myProspects = $client->prospects->list([
    'filter' => [
        'assigned_organization_user_id' => 'org_user_xxx',
    ],
]);

// By project
$projectProspects = $client->prospects->list([
    'filter' => [
        'assigned_organization_project_id' => 'org_proj_xxx',
    ],
]);

// By source
$inboundLeads = $client->prospects->list([
    'filter' => [
        'source_type' => 'inbound',
    ],
]);

// By linked customer
$linkedProspects = $client->prospects->list([
    'filter' => [
        'linked_organization_user_id' => 'org_user_xxx',
    ],
]);
```

### With Related Data

```php
<?php

$prospects = $client->prospects->list([
    'include' => ['status', 'assigned_user', 'activities', 'project', 'linked_user'],
]);

foreach ($prospects as $prospect) {
    echo "{$prospect->title}\n";

    if ($prospect->status) {
        echo "  Status: {$prospect->status->name}\n";
    }

    if ($prospect->assigned_user) {
        $name = $prospect->assigned_user->first_name ?? $prospect->assigned_user->name;
        echo "  Assigned to: {$name}\n";
    }

    echo "  Activities: " . count($prospect->activities ?? []) . "\n";
}
```

## Kanban Board View

Get prospects organized by status for a kanban board:

```php
<?php

$board = $client->prospects->board();

foreach ($board->columns as $column) {
    echo "=== {$column->status->name} ({$column->count}) ===\n";

    foreach ($column->prospects as $prospect) {
        echo "  - {$prospect->title}\n";
    }
}
```

## Retrieving a Prospect

```php
<?php

$prospect = $client->prospects->retrieve('org_pros_xxx', [
    'include' => ['status', 'assigned_user', 'activities', 'linked_user'],
]);

echo "Prospect: {$prospect->title}\n";
echo "Contact: {$prospect->first_name} {$prospect->last_name}\n";
echo "Company: {$prospect->company_name}\n";
echo "Email: {$prospect->email}\n";

if ($prospect->status) {
    echo "Status: {$prospect->status->name}\n";
}

echo "Budget: {$prospect->budget} {$prospect->budget_currency}\n";
echo "Summary: {$prospect->summary}\n";

if ($prospect->linked_user) {
    echo "Linked to customer: {$prospect->linked_user->id}\n";
}
```

## Updating a Prospect

```php
<?php

$prospect = $client->prospects->update('org_pros_xxx', [
    'budget' => '85000',
    'summary' => 'Updated scope: includes mobile app development.',
]);
```

### Mark as Won/Lost

```php
<?php

// Mark as won
$prospect = $client->prospects->update('org_pros_xxx', [
    'state_won_at' => date('Y-m-d H:i:s'),
]);

// Mark as lost with reason
$prospect = $client->prospects->update('org_pros_xxx', [
    'state_lost_at' => date('Y-m-d H:i:s'),
    'state_lost_reason' => 'Budget constraints - competitor offered lower price',
]);

// Mark as disqualified
$prospect = $client->prospects->update('org_pros_xxx', [
    'state_disqualified_at' => date('Y-m-d H:i:s'),
    'state_disqualified_reason' => 'Not a good fit for our services',
]);
```

## Managing Pipeline Status

### List Statuses

```php
<?php

$statuses = $client->prospectStatuses->list();

foreach ($statuses as $status) {
    echo "{$status->order}. {$status->name}\n";
}

// Typical output:
// 1. New
// 2. Contacted
// 3. Qualified
// 4. Proposal Sent
// 5. Negotiation
// 6. Won
// 7. Lost
```

### Create Custom Status

```php
<?php

$status = $client->prospectStatuses->create([
    'name' => 'Technical Review',
    'color' => '#9C27B0',
    'order' => 4,
    'is_won' => false,
    'is_lost' => false,
]);
```

### Move Prospect to Status

```php
<?php

$prospect = $client->prospects->update('org_pros_xxx', [
    'organization_prospect_status_id' => 'org_pros_status_qualified_xxx',
]);

echo "Moved to: {$prospect->status->name}\n";
```

### Advance to Next Status

```php
<?php

// Automatically move to the next status in the pipeline
$prospect = $client->prospects->advance('org_pros_xxx', [
    'note' => 'Client confirmed budget and timeline.',
]);

echo "Advanced to: {$prospect->status->name}\n";
```

## Prospect Activities

Track interactions with prospects.

### Add Activity

```php
<?php

$activity = $client->prospectActivities->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'type' => 'call',
    'title' => 'Discovery Call',
    'description' => 'Discussed requirements and timeline. Client interested in Q2 start.',
    'occurred_at' => '2026-02-05 14:00:00',
    'duration_minutes' => 45,
]);

echo "Activity logged: {$activity->title}\n";
```

### Activity Types

```php
<?php

// Call
$client->prospectActivities->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'type' => 'call',
    'title' => 'Follow-up call',
    'description' => 'Discussed proposal feedback.',
]);

// Email
$client->prospectActivities->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'type' => 'email',
    'title' => 'Sent proposal',
    'description' => 'Proposal document sent via email.',
]);

// Meeting
$client->prospectActivities->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'type' => 'meeting',
    'title' => 'On-site demo',
    'description' => 'Product demonstration at client office.',
    'duration_minutes' => 120,
]);

// Note
$client->prospectActivities->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'type' => 'note',
    'title' => 'Internal note',
    'description' => 'Decision maker is the CTO, not the IT Manager.',
]);

// Task
$client->prospectActivities->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'type' => 'task',
    'title' => 'Send case study',
    'description' => 'Client requested case study from similar industry.',
    'due_at' => '2026-02-10',
]);
```

### List Activities

```php
<?php

$activities = $client->prospectActivities->list([
    'filter' => [
        'organization_prospect_id' => 'org_pros_xxx',
    ],
    'sort' => '-occurred_at',
]);

foreach ($activities as $activity) {
    echo "[{$activity->type}] {$activity->title}\n";
    echo "  {$activity->occurred_at}: {$activity->description}\n";
}
```

## Importing Prospects

Bulk import prospects from CSV or other sources.

### Start Import

```php
<?php

$import = $client->prospects->import([
    'file' => fopen('prospects.csv', 'r'),
    'mapping' => [
        'Company' => 'company_name',
        'Contact Name' => 'first_name',
        'Email' => 'email',
        'Phone' => 'phone_number',
        'Deal Value' => 'budget',
    ],
]);

echo "Import started: {$import->id}\n";
```

### Check Import Progress

```php
<?php

$progress = $client->prospects->importProgress($import->id);

echo "Status: {$progress->status}\n";
echo "Processed: {$progress->processed_count} / {$progress->total_count}\n";
echo "Success: {$progress->success_count}\n";
echo "Failed: {$progress->failed_count}\n";
```

## Deleting a Prospect

```php
<?php

$prospect = $client->prospects->delete('org_pros_xxx');

echo "Deleted at: {$prospect->deleted_at}\n";
```

## Restoring a Prospect

```php
<?php

$prospect = $client->prospects->restore('org_pros_xxx');

echo "Restored: {$prospect->title}\n";
```

## Field Reference

### Required Fields

| Field | Description |
|-------|-------------|
| `first_name` | First name (required if no `company_name`) |
| `company_name` | Company name (required if no `first_name`) |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `title` | string | Deal/opportunity title |
| `last_name` | string | Last name |
| `email` | string | Email address |
| `phone_number` | string | Phone number |
| `phone_number_country_code` | string | Phone country code |
| `country_code` | string | Country code |
| `social_profiles` | array | Social media profiles |
| `budget` | string | Budget amount (as string) |
| `budget_currency` | string | Budget currency (ISO 4217) |
| `summary` | string | Deal summary/notes |
| `source_type` | string | Lead source type |
| `source_channel` | string | Lead source channel |
| `source_campaign` | string | Marketing campaign |
| `source_referrer_organization_user_id` | string | Referrer user ID |
| `organization_prospect_status_id` | string | Pipeline status ID |
| `assigned_organization_user_id` | string | Assigned sales rep ID |
| `assigned_organization_project_id` | string | Assigned project ID |
| `linked_organization_user_id` | string | Linked customer ID |
| `state_qualified_at` | datetime | When qualified |
| `state_disqualified_at` | datetime | When disqualified |
| `state_disqualified_reason` | string | Disqualification reason |
| `state_won_at` | datetime | When won |
| `state_lost_at` | datetime | When lost |
| `state_lost_reason` | string | Loss reason |

## Complete Example: Sales Pipeline Workflow

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create new prospect from inbound lead
    $prospect = $client->prospects->create([
        'title' => 'E-commerce Platform Development',
        'first_name' => 'Emily',
        'last_name' => 'Chen',
        'email' => 'emily.chen@retailco.com',
        'company_name' => 'RetailCo',
        'phone_number' => '555987654',
        'phone_number_country_code' => 'US',
        'country_code' => 'US',
        'budget' => '120000',
        'budget_currency' => 'USD',
        'source_type' => 'inbound',
        'source_channel' => 'website',
        'summary' => 'Needs new e-commerce platform to replace legacy system.',
    ]);

    echo "New prospect: {$prospect->id}\n";

    // 2. Log initial contact
    $client->prospectActivities->create([
        'organization_prospect_id' => $prospect->id,
        'type' => 'email',
        'title' => 'Initial response',
        'description' => 'Sent introduction email with company overview.',
    ]);

    // 3. After discovery call - qualify and log
    $client->prospectActivities->create([
        'organization_prospect_id' => $prospect->id,
        'type' => 'call',
        'title' => 'Discovery call',
        'description' => 'Discussed requirements. Timeline: Q3 launch. Budget confirmed.',
        'duration_minutes' => 60,
    ]);

    $prospect = $client->prospects->update($prospect->id, [
        'state_qualified_at' => date('Y-m-d H:i:s'),
    ]);

    $prospect = $client->prospects->advance($prospect->id, [
        'note' => 'Qualified - budget and timeline confirmed.',
    ]);

    echo "Advanced to: {$prospect->status->name}\n";

    // 4. Send proposal and advance
    $client->prospectActivities->create([
        'organization_prospect_id' => $prospect->id,
        'type' => 'email',
        'title' => 'Proposal sent',
        'description' => 'Sent detailed proposal with three pricing options.',
    ]);

    $prospect = $client->prospects->advance($prospect->id, [
        'note' => 'Proposal sent - awaiting feedback.',
    ]);

    // 5. Convert to customer when won
    // (In real workflow, this would happen after status changes to 'Won')

    // Get customer role
    $roles = $client->userRoles->list([
        'filter' => ['can_be_invoiced' => true, 'is_business_entity' => true],
    ]);
    $customerRole = $roles->data[0];

    // Create organization user from prospect data
    $customer = $client->organizationUsers->create([
        'name' => $prospect->company_name,
        'email' => $prospect->email,
        'country_code' => $prospect->country_code,
        'phone_number' => $prospect->phone_number,
        'phone_number_country_code' => $prospect->phone_number_country_code,
        'organization_user_role_id' => $customerRole->id,
    ]);

    // Link prospect to customer and mark as won
    $prospect = $client->prospects->update($prospect->id, [
        'linked_organization_user_id' => $customer->id,
        'state_won_at' => date('Y-m-d H:i:s'),
    ]);

    echo "Prospect converted to customer: {$customer->id}\n";
    echo "Ready to create invoices and contracts!\n";

} catch (ValidationException $e) {
    echo "Error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Organization Users](organization-users.md) - Create customers from won prospects
- [Proposals](proposals.md) - Send formal proposals to prospects
- [Projects](projects.md) - Manage prospects within projects
