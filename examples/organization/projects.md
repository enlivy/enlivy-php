# Projects

Organize work with projects, team members, and permissions. Projects support multilingual titles and descriptions with locale configuration.

## Key Concepts

### Multilingual Support

Projects use `title_lang_map` and `description_lang_map` for multilingual content:

```php
'title_lang_map' => [
    'en' => 'Website Redesign Q1 2026',
    'ro' => 'Redesign Website Q1 2026',
],
'description_lang_map' => [
    'en' => 'Complete redesign of company website',
    'ro' => 'Redesign complet al website-ului companiei',
]
```

### Locale Configuration

Projects can specify available locales and a default locale:
- `locale_list`: Array of available locales for the project
- `locale`: Default/primary locale (must be in locale_list)

## Creating a Project

### Basic Project

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$project = $client->projects->create([
    // Required multilingual fields
    'title_lang_map' => [
        'en' => 'Website Redesign Q1 2026',
    ],
    'description_lang_map' => [
        'en' => 'Complete redesign of company website',
    ],
]);

echo "Project created: {$project->id}\n";
```

### Multilingual Project

```php
<?php

$project = $client->projects->create([
    'title_lang_map' => [
        'en' => 'Enterprise CRM Implementation',
        'ro' => 'Implementare CRM Enterprise',
        'de' => 'Enterprise CRM Implementierung',
    ],
    'description_lang_map' => [
        'en' => 'Full CRM rollout for BigCorp including customizations and training',
        'ro' => 'Implementare completa CRM pentru BigCorp incluzand personalizari si training',
        'de' => 'Vollstandige CRM-Einfuhrung fur BigCorp mit Anpassungen und Schulung',
    ],

    // Locale configuration
    'locale' => 'en',                    // Default locale
    'locale_list' => ['en', 'ro', 'de'], // Available locales
]);

echo "Project created: {$project->id}\n";
```

## Listing Projects

### Basic List

```php
<?php

$projects = $client->projects->list();

foreach ($projects as $project) {
    $title = $project->title_lang_map['en'] ?? array_values($project->title_lang_map)[0] ?? 'Untitled';
    echo "{$project->id}: {$title}\n";
}
```

### With Related Data

```php
<?php

$projects = $client->projects->list([
    'include' => ['resource_bundles', 'tag_ids'],
]);

foreach ($projects as $project) {
    $title = $project->title_lang_map[$project->locale ?? 'en'] ?? 'Untitled';
    echo "{$title}\n";

    if (!empty($project->resource_bundles)) {
        echo "  Resource bundles: " . count($project->resource_bundles) . "\n";
    }
}
```

### Pagination

```php
<?php

$projects = $client->projects->list([
    'page' => 1,
    'per_page' => 25,
]);

echo "Total: {$projects->getTotalCount()}\n";
echo "Page {$projects->getCurrentPage()} of {$projects->getTotalPages()}\n";
```

## Retrieving a Project

```php
<?php

$project = $client->projects->retrieve('org_proj_xxx', [
    'include' => ['resource_bundles', 'tag_ids'],
]);

echo "Project: {$project->id}\n";

// Display titles in all languages
echo "Titles:\n";
foreach ($project->title_lang_map as $lang => $title) {
    $isPrimary = ($lang === $project->locale) ? ' (primary)' : '';
    echo "  [{$lang}]: {$title}{$isPrimary}\n";
}

// Display descriptions
echo "Descriptions:\n";
foreach ($project->description_lang_map as $lang => $desc) {
    echo "  [{$lang}]: {$desc}\n";
}

// Display locale configuration
if ($project->locale) {
    echo "Default locale: {$project->locale}\n";
}
if (!empty($project->locale_list)) {
    echo "Available locales: " . implode(', ', $project->locale_list) . "\n";
}
```

## Updating a Project

```php
<?php

$project = $client->projects->update('org_proj_xxx', [
    'title_lang_map' => [
        'en' => 'Website Redesign Q2 2026',
        'ro' => 'Redesign Website Q2 2026',
    ],
    'description_lang_map' => [
        'en' => 'Updated project description',
    ],
]);

echo "Updated project: {$project->id}\n";
```

## Deleting a Project

```php
<?php

// Soft delete
$project = $client->projects->delete('org_proj_xxx');

echo "Deleted at: {$project->deleted_at}\n";
```

## Restoring a Project

```php
<?php

$project = $client->projects->restore('org_proj_xxx');

echo "Restored: {$project->id}\n";
```

## Tagging Projects

```php
<?php

// Add tags
$project = $client->projects->tag('org_proj_xxx', [
    'tags' => ['priority', 'q1-2026'],
]);

// Remove tags
$project = $client->projects->untag('org_proj_xxx', [
    'tags' => ['priority'],
]);
```

## Project Members

Project members link organization users to projects.

### Add Member

```php
<?php

$member = $client->projectMembers->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_user_id' => 'org_user_xxx',
]);

echo "Added member: {$member->id}\n";
```

### List Members

```php
<?php

$members = $client->projectMembers->list([
    'filter' => [
        'organization_project_id' => 'org_proj_xxx',
    ],
    'include' => ['organization_user'],
]);

foreach ($members as $member) {
    $userName = $member->organization_user->name ??
                "{$member->organization_user->first_name} {$member->organization_user->last_name}";
    echo "- {$userName}\n";
}
```

### Remove Member

```php
<?php

$client->projectMembers->delete('org_proj_member_xxx');

echo "Member removed\n";
```

## Project Permissions

Control access to various resources within a project.

### Guideline Access

```php
<?php

// Grant guideline access
$permission = $client->projectPermissionGuidelines->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_guideline_id' => 'org_guide_xxx',
]);

// List guidelines accessible in project
$permissions = $client->projectPermissionGuidelines->list([
    'filter' => ['organization_project_id' => 'org_proj_xxx'],
]);

foreach ($permissions as $p) {
    echo "Guideline: {$p->organization_guideline_id}\n";
}
```

### Playbook Access

```php
<?php

// Grant playbook access
$permission = $client->projectPermissionPlaybooks->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_playbook_id' => 'org_play_xxx',
]);

// List playbooks accessible in project
$permissions = $client->projectPermissionPlaybooks->list([
    'filter' => ['organization_project_id' => 'org_proj_xxx'],
]);
```

### Report Access

```php
<?php

// Grant report access
$permission = $client->projectPermissionReports->create([
    'organization_project_id' => 'org_proj_xxx',
    'organization_report_id' => 'org_report_xxx',
]);

// List reports accessible in project
$permissions = $client->projectPermissionReports->list([
    'filter' => ['organization_project_id' => 'org_proj_xxx'],
]);
```

## Resource Bundles

Link resource bundles to projects:

```php
<?php

$projects = $client->projects->list([
    'include' => ['resource_bundles', 'project_resource_bundles'],
]);

foreach ($projects as $project) {
    echo "Project: " . ($project->title_lang_map['en'] ?? 'Untitled') . "\n";

    if (!empty($project->resource_bundles)) {
        echo "  Resource bundles:\n";
        foreach ($project->resource_bundles as $bundle) {
            echo "    - {$bundle->id}\n";
        }
    }
}
```

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `title_lang_map` | object | Project title by language |
| `description_lang_map` | object | Project description by language |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `locale` | string | Default locale (must be in locale_list) |
| `locale_list` | array | Available locales for the project |

### Include Options

| Include | Description |
|---------|-------------|
| `organization` | Organization details |
| `tag_ids` | Associated tag IDs |
| `resource_bundles` | Linked resource bundles |
| `project_resource_bundles` | Project-resource bundle links |
| `deleted_by_user` | User who deleted (if soft-deleted) |

## Complete Example: Project Setup

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create project
    $project = $client->projects->create([
        'title_lang_map' => [
            'en' => 'Enterprise CRM Implementation',
            'ro' => 'Implementare CRM Enterprise',
        ],
        'description_lang_map' => [
            'en' => 'Full CRM rollout for BigCorp including customizations and training',
            'ro' => 'Implementare completa CRM incluzand personalizari si training',
        ],
        'locale' => 'en',
        'locale_list' => ['en', 'ro'],
    ]);

    echo "Project created: {$project->id}\n";

    // 2. Add team members
    $teamMembers = ['org_user_pm_xxx', 'org_user_dev1_xxx', 'org_user_dev2_xxx'];

    foreach ($teamMembers as $userId) {
        $client->projectMembers->create([
            'organization_project_id' => $project->id,
            'organization_user_id' => $userId,
        ]);
    }

    echo "Added " . count($teamMembers) . " team members\n";

    // 3. Grant resource access
    $client->projectPermissionGuidelines->create([
        'organization_project_id' => $project->id,
        'organization_guideline_id' => 'org_guide_xxx',
    ]);

    $client->projectPermissionPlaybooks->create([
        'organization_project_id' => $project->id,
        'organization_playbook_id' => 'org_play_xxx',
    ]);

    // 4. Tag project
    $client->projects->tag($project->id, [
        'tags' => ['enterprise', '2026', 'crm'],
    ]);

    echo "Project setup complete!\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Prospects](prospects.md) - Manage prospects within projects
- [Organization Users](organization-users.md) - Team members
- [Reports](reports.md) - Reports accessible in projects
