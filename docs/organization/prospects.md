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

    // Where they are on the web (optional array)
    'links' => [
        ['kind' => 'website', 'url' => 'https://bigcorp.example'],
        ['kind' => 'linkedin', 'url' => 'https://linkedin.com/in/sarah-johnson'],
    ],

    // Deal info (budget is string, not numeric)
    'budget' => '75000',
    'budget_currency' => 'USD',
    'summary' => 'Looking for a complete CRM solution with custom integrations.',

    // Source tracking
    'source_type' => 'inbound', // inbound, outbound, referral, etc.
    'source_channel' => 'website',
    'source_medium' => 'cpc',
    'source_campaign' => 'google-ads-q1',
    'source_term' => 'accounting software',
    'source_content' => 'hero-cta-b',
    'source_click_id' => 'GCLID-xxx',
    'source_referrer_organization_user_id' => 'org_user_referrer_xxx', // Optional referrer

    // Pipeline position
    'organization_prospect_stage_id' => 'org_pros_stage_qualified_xxx',

    // Assignment
    'assigned_organization_user_id' => 'org_user_sales_rep_xxx',
    'assigned_organization_project_id' => 'org_proj_xxx',

    // Link to existing customer (if converting or re-engaging)
    'linked_organization_user_id' => 'org_user_xxx',
]);

echo "Created: {$prospect->title}\n";
echo "Budget: {$prospect->budget} {$prospect->budget_currency}\n";
```

Each link's `kind` is one of `Enums\WebLinkKinds` — `website` for a page the prospect publishes,
`directory` for a listing somebody else publishes about them, or a named network such as
`linkedin` or `github`. A `url` typed without a scheme gets `https://`. Up to 25 links, each with
an optional `label`; sending `links` replaces the whole list, since a link has no id to match on.

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
    'organization_prospect_stage_id' => 'org_pros_stage_qualified_xxx',
]);

// By assigned user
$myProspects = $client->prospects->list([
    'assigned_organization_user_id' => 'org_user_xxx',
]);

// By project
$projectProspects = $client->prospects->list([
    'assigned_organization_project_id' => 'org_proj_xxx',
]);

// By source
$inboundLeads = $client->prospects->list([
    'source_type' => 'inbound',
]);

// By campaign attribution
$campaignLeads = $client->prospects->list([
    'source_channel' => 'google',
    'source_medium' => 'cpc',
    'source_campaign' => 'spring-2026',
]);

// Only prospects sitting past their status's stuck threshold
$stalled = $client->prospects->list([
    'is_stalled' => true,
]);

// By linked customer
$linkedProspects = $client->prospects->list([
    'linked_organization_user_id' => 'org_user_xxx',
]);
```

### With Related Data

```php
<?php

$prospects = $client->prospects->list([
    'include' => ['organization_prospect_stage', 'assigned_organization_user', 'assigned_organization_project', 'linked_organization_user'],
]);

foreach ($prospects as $prospect) {
    echo "{$prospect->title}\n";

    if ($prospect->organization_prospect_stage) {
        echo "  Stage: {$prospect->organization_prospect_stage->title_lang_map['en']}\n";
    }

    if ($prospect->assigned_organization_user) {
        $name = $prospect->assigned_organization_user->first_name ?? $prospect->assigned_organization_user->name;
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

// Narrowed to a project and a date window
$board = $client->prospects->board([
    'assigned_organization_project_id' => 'org_proj_xxx',
    'created_at_from' => '2026-08-01T00:00:00Z',
    'created_at_to' => '2026-08-31T23:59:59Z',
]);

foreach ($board->columns as $column) {
    echo "=== {$column->stage->title_lang_map['en']} ({$column->count}) ===\n";

    foreach ($column->prospects as $prospect) {
        echo "  - {$prospect->title}\n";
    }
}
```

## Pipeline Analytics

Aggregate views over the pipeline. `start_date` and `end_date` are required; the window can be
narrowed by owner, project or source, and money can be reported in a single currency.

```php
<?php

$window = [
    'start_date' => '2026-08-01T00:00:00Z',
    'end_date' => '2026-08-31T23:59:59Z',
    'convert_to_currency' => 'EUR',
];

$overview = $client->analytics->prospects($window);

$summary     = $client->analytics->prospectsByType('summary', $window);
$funnel      = $client->analytics->prospectsByType('funnel', $window);
$transitions = $client->analytics->prospectsByType('transitions', $window);
$stalled     = $client->analytics->prospectsByType('stalled', $window);
```

| Type | Answers |
|------|---------|
| `summary` | Totals, and breakdowns by status, status type, owner, project, source type, channel, campaign, and budget |
| `funnel` | How a cohort moved through the stages, including its proposal and paid stages, plus what could not be attributed |
| `transitions` | Status-to-status movement counts, with how long prospects sat in the status they left |
| `stalled` | Per status: how many are stuck past its threshold, and how long they have sat |

Narrowing accepts `assigned_organization_user_id`, `assigned_organization_project_id` and
`source_type`. Responses are untyped — read them as plain objects.

## Retrieving a Prospect

```php
<?php

$prospect = $client->prospects->retrieve('org_pros_xxx', [
    'include' => ['organization_prospect_stage', 'assigned_organization_user', 'linked_organization_user'],
]);

echo "Prospect: {$prospect->title}\n";
echo "Contact: {$prospect->first_name} {$prospect->last_name}\n";
echo "Company: {$prospect->company_name}\n";
echo "Email: {$prospect->email}\n";

if ($prospect->organization_prospect_stage) {
    echo "Stage: {$prospect->organization_prospect_stage->title_lang_map['en']}\n";
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

## Pipelines and Stages

A **pipeline** holds an ordered set of **stages**; a prospect sits in exactly one stage,
and each pipeline has its own kanban board. Before 3.1.0 stages were called *statuses* —
the wire names, paths and SDK classes all changed, with no aliases. See the
[3.1.0 changelog entry](../../CHANGELOG.md) for the full rename map.

### List Pipelines

```php
<?php

$pipelines = $client->prospectPipelines->list([
    'include' => 'stages',
]);

foreach ($pipelines->data as $pipeline) {
    echo "{$pipeline->order}. {$pipeline->title_lang_map['en']}\n";
}
```

### Create a Pipeline

```php
<?php

$pipeline = $client->prospectPipelines->create([
    'title_lang_map' => ['en' => 'Enterprise'],
    'description_lang_map' => ['en' => 'Deals over 50k'],
    'rgba_color_code' => '156, 39, 176, 1',
    'order' => 2,
]);
```

### Board for One Pipeline

```php
<?php

$board = $client->prospectPipelines->board($pipeline->id, [
    'stage_types' => 'open',
    'assigned_organization_user_id' => 'org_user_xxx',
]);
```

### List Stages

```php
<?php

$stages = $client->prospectStages->list([
    'organization_prospect_pipeline_id' => $pipeline->id,
    'include' => 'pipeline',
]);

foreach ($stages->data as $stage) {
    echo "{$stage->order}. {$stage->title_lang_map['en']} ({$stage->stage_type})\n";
}
```

### Create a Stage

```php
<?php

$stage = $client->prospectStages->create([
    'organization_prospect_pipeline_id' => $pipeline->id,
    'title_lang_map' => ['en' => 'Technical Review'],
    'stage_type' => \Enlivy\Enums\Prospect\StageTypes::OPEN->value,
    'rgba_color_code' => '156, 39, 176, 1',
    'order' => 4,
    'is_stuck_threshold_days' => 14,
]);
```

`is_stuck_threshold_days` is a day count, not a flag: a prospect sitting in the stage
longer than that is reported as stalled by the `is_stalled` filter and the analytics lane.

### Move a Prospect to a Stage

```php
<?php

$prospect = $client->prospects->update('org_pros_xxx', [
    'organization_prospect_stage_id' => 'org_pros_stage_qualified_xxx',
]);
```

### Advance to the Next Stage

```php
<?php

$prospect = $client->prospects->advance('org_pros_xxx', [
    'note' => 'Client confirmed budget and timeline.',
]);
```

### Arrange Cards Within a Column

A board column is read a page at a time, so a card is placed between neighbours rather than at an
index. `moveOnBoard()` reorders within the prospect's own column; changing column is `advance()` or
a stage update.

```php
<?php

$prospect = $client->prospects->moveOnBoard('org_pros_xxx', [
    'previous_organization_prospect_id' => 'org_pros_above',
    'next_organization_prospect_id' => 'org_pros_below',
]);

// Or to either end of the column
$client->prospects->moveOnBoard('org_pros_xxx', ['place' => 'top']);
```

The board sorts by `board_rank`, then newest first for cards nobody has placed. `board_rank` is
read-only and only `moveOnBoard()` sets it. A rank is lowercase letters and digits, so a plain
string comparison orders it as the server does. Sort any cards you cache that way, and name
neighbours in that order, or the move is refused.

## Finding and Merging Duplicates

`duplicates()` scores other prospects against one record; `merge()` folds them in.

```php
<?php

$candidates = $client->prospects->duplicates('org_pros_xxx');

foreach ($candidates->data as $candidate) {
    echo "{$candidate->organization_prospect_id}: {$candidate->confidence}\n";
    echo "  matched on: " . implode(', ', $candidate->signals) . "\n";

    if ($candidate->blockers !== []) {
        echo "  cannot merge: " . implode(', ', $candidate->blockers) . "\n";
    }
}
```

A candidate with a non-empty `blockers` list cannot be merged until the conflict is resolved.
`organization_proposals_count` and `organization_prospect_activities_count` tell you how much
history each candidate carries, so you can pick which record survives.

```php
<?php

$survivor = $client->prospects->merge('org_pros_xxx', [
    'merge_organization_prospect_ids' => ['org_pros_yyy', 'org_pros_zzz'],
    'field_choices' => [
        'email' => 'org_pros_yyy',
        'phone_number' => 'org_pros_xxx',
    ],
]);
```

`field_choices` decides, per field, which of the merged records wins. Anything left out
keeps the surviving prospect's own value. The merged records are deleted, and their
activities and proposals reattach to the survivor.

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
    'organization_prospect_id' => 'org_pros_xxx',
    'sort' => '-occurred_at',
]);

foreach ($activities as $activity) {
    echo "[{$activity->type}] {$activity->title}\n";
    echo "  {$activity->occurred_at}: {$activity->description}\n";
}

// Narrow to when the activity happened, rather than when it was recorded
$thisWeek = $client->prospectActivities->list([
    'activity_at_from' => '2026-08-17T00:00:00Z',
    'activity_at_to' => '2026-08-24T00:00:00Z',
]);
```

An activity that records a status move carries the pair it moved between —
`from_organization_prospect_stage_id` and `to_organization_prospect_stage_id` — so a history can be
read without resolving the status path. Both are null on activities that are not moves.

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
| `links` | array | Where the prospect is on the web (`kind`, `url`, optional `label`) |
| `budget` | string | Budget amount (as string) |
| `budget_currency` | string | Budget currency (ISO 4217) |
| `summary` | string | Deal summary/notes |
| `source_type` | string | Lead source type |
| `source_channel` | string | Lead source channel |
| `source_medium` | string | Delivery medium the lead arrived through, e.g. `cpc`, `email` |
| `source_campaign` | string | Marketing campaign |
| `source_term` | string | Paid keyword the click was bought on |
| `source_content` | string | Which creative or link variant was clicked |
| `source_click_id` | string | Ad-network click identifier, for matching spend back to the lead |
| `source_referrer_organization_user_id` | string | Referrer user ID |
| `organization_prospect_stage_id` | string | Pipeline stage ID |
| `assigned_organization_user_id` | string | Assigned sales rep ID |
| `assigned_organization_project_id` | string | Assigned project ID |
| `linked_organization_user_id` | string | Linked customer ID |
| `state_qualified_at` | datetime | When qualified |
| `state_disqualified_at` | datetime | When disqualified |
| `state_disqualified_reason` | string | Disqualification reason |
| `state_won_at` | datetime | When won |
| `state_lost_at` | datetime | When lost |
| `state_lost_reason` | string | Loss reason |

`links` and every `source_*` field except `source_referrer_organization_user_id` are writable on the
customer-portal lane too (`$portal->prospects->create()` / `->update()`), so a lead captured through
a portal form carries the same attribution as one created through the back office.

`source_channel` is capped at 100 characters on both lanes. The portal lane previously accepted 255
and now matches the back office, so an over-long value that used to be stored is a 422.

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

    echo "Advanced to: {$prospect->organization_prospect_stage_id}\n";

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
        'can_be_invoiced' => true,
        'is_business_entity' => true,
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

- [Organization Users](users.md) - Create customers from won prospects
- [Proposals](proposals.md) - Send formal proposals to prospects
- [Projects](projects.md) - Manage prospects within projects
- [Data Imports](data-imports.md) - Bulk-load leads from a CSV file
- [Tasks](tasks.md) - Link follow-up work to a prospect (`organization_tasks` include)
