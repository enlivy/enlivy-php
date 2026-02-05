# Projects

Organize work with projects, team members, and permissions.

## Creating a Project

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$project = $client->projects->create([
    'name' => 'Website Redesign Q1 2026',
    'description' => 'Complete redesign of company website',
    'start_date' => '2026-02-01',
    'end_date' => '2026-04-30',
    'budget' => 50000.00,
    'budget_currency' => 'EUR',
    'is_active' => true,
]);

echo "Project created: {$project->id}\n";
```

## Listing Projects

```php
<?php

$projects = $client->projects->list([
    'filter' => ['is_active' => true],
    'include' => ['members'],
]);

foreach ($projects as $project) {
    echo "{$project->name}\n";
    echo "  Members: " . count($project->members ?? []) . "\n";
}
```

## Project Members

### Add Member

```php
<?php

$member = $client->projectMembers->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_user_id' => 'org_user_xxx',
    'role' => 'developer',
]);
```

### List Members

```php
<?php

$members = $client->projectMembers->list([
    'filter' => ['organization_project_id' => 'org_proj_xxx'],
    'include' => ['user'],
]);

foreach ($members as $member) {
    echo "{$member->user->first_name} - {$member->role}\n";
}
```

### Remove Member

```php
<?php

$client->projectMembers->delete('org_proj_member_xxx');
```

## Project Permissions

Control access to resources within a project.

### Prospect Access

```php
<?php

// Grant project access to specific prospects
$permission = $client->projectPermissionProspects->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_prospect_id' => 'org_pros_xxx',
]);

// List prospects accessible in project
$permissions = $client->projectPermissionProspects->list([
    'filter' => ['organization_project_id' => 'org_proj_xxx'],
]);
```

### Guideline Access

```php
<?php

$permission = $client->projectPermissionGuidelines->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_guideline_id' => 'org_guide_xxx',
]);
```

### Playbook Access

```php
<?php

$permission = $client->projectPermissionPlaybooks->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_playbook_id' => 'org_play_xxx',
]);
```

### Report Access

```php
<?php

$permission = $client->projectPermissionReports->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_report_id' => 'org_report_xxx',
]);
```

## Project-Specific Prospect Statuses

Projects can have their own prospect status pipeline:

```php
<?php

// Create project-specific status
$status = $client->projectProspectStatuses->create([
    'organization_project_id' => 'org_proj_xxx',
    'name' => 'Technical Review',
    'color' => '#9C27B0',
    'order' => 3,
]);

// List project statuses
$statuses = $client->projectProspectStatuses->list([
    'filter' => ['organization_project_id' => 'org_proj_xxx'],
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

// 1. Create project
$project = $client->projects->create([
    'name' => 'Enterprise CRM Implementation',
    'description' => 'Full CRM rollout for BigCorp',
    'start_date' => '2026-03-01',
    'end_date' => '2026-08-31',
    'budget' => 150000.00,
    'budget_currency' => 'EUR',
]);

// 2. Add team members
$client->projectMembers->create([
    'organization_project_id' => $project->id,
    'organization_user_id' => 'org_user_pm_xxx',
    'role' => 'project_manager',
]);

$client->projectMembers->create([
    'organization_project_id' => $project->id,
    'organization_user_id' => 'org_user_dev_xxx',
    'role' => 'developer',
]);

// 3. Set up project-specific pipeline
$statuses = ['Discovery', 'Analysis', 'Development', 'Testing', 'Deployment'];
foreach ($statuses as $i => $name) {
    $client->projectProspectStatuses->create([
        'organization_project_id' => $project->id,
        'name' => $name,
        'order' => $i + 1,
    ]);
}

// 4. Assign prospect to project
$prospect = $client->prospects->update('org_pros_xxx', [
    'assigned_organization_project_id' => $project->id,
]);

echo "Project setup complete!\n";
```

## Related

- [Prospects](prospects.md) - Manage prospects within projects
- [Organization Users](organization-users.md) - Team members
