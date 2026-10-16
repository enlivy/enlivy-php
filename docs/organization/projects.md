# Projects

Organize work with projects and team members. Projects support multilingual titles and descriptions with locale configuration.

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

    if (!empty($project->resource_bundles->data)) {
        echo "  Resource bundles: " . count($project->resource_bundles->data) . "\n";
    }
}
```

### Pagination

```php
<?php

$projects = $client->projects->list([
    'page' => 1,
    'limit' => 25,
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

// Add existing tags by id or alias (create them with $client->tags)
$client->projects->addTag('org_proj_xxx', 'priority');
$client->projects->addTag('org_proj_xxx', 'q1-2026');

// Remove tags
$client->projects->removeTag('org_proj_xxx', 'priority');
```

## Project Members

Project members link organization users to projects. A member may hold one of the organization's
project roles, which access grants can name (see [Portal Access](portal-access.md)).

### Add Member

```php
<?php

$member = $client->projectMembers->create('org_proj_xxx', [
    'organization_user_id' => 'org_user_xxx',
    'organization_project_role_id' => 'org_proj_role_xxx',
]);

echo "Added member: {$member->id}\n";
```

### List Members

```php
<?php

$members = $client->projectMembers->list('org_proj_xxx', [
    'include' => ['organization_user', 'organization_project_role'],
]);

foreach ($members as $member) {
    echo "- {$member->organization_user?->data->name}\n";
}
```

### Change a Member's Role

```php
<?php

$client->projectMembers->update('org_proj_xxx', 'org_user_xxx', [
    'organization_project_role_id' => 'org_proj_role_xxx',
]);
```

### Remove Member

```php
<?php

$client->projectMembers->delete('org_proj_xxx', 'org_user_xxx');
```

## Portal Access

What a project's members reach in the customer portal is set with access grants, which name a project
role, every member of a project, or one person. Resource bundles apply a set of grants to a project.
See [Portal Access](portal-access.md).

```php
<?php

$project = $client->projects->retrieve('org_proj_xxx', [
    'include' => ['resource_bundles', 'resource_bundle_attachments'],
]);

foreach ($project->resource_bundle_attachments->data ?? [] as $attachment) {
    echo "Bundle {$attachment->organization_resource_bundle_id} (attachment {$attachment->id})\n";
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
| `resource_bundles` | Resource bundles attached to the project |
| `resource_bundle_attachments` | The attachments, with the ids detaching needs |
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

    // 2. Add team members as contractors
    $contractorRoleId = null;
    foreach ($client->projectRoles->list() as $role) {
        if ($role->alias === 'contractor') {
            $contractorRoleId = $role->id;
        }
    }

    $teamMembers = ['org_user_pm_xxx', 'org_user_dev1_xxx', 'org_user_dev2_xxx'];

    foreach ($teamMembers as $userId) {
        $client->projectMembers->create($project->id, [
            'organization_user_id' => $userId,
            'organization_project_role_id' => $contractorRoleId,
        ]);
    }

    echo "Added " . count($teamMembers) . " team members\n";

    // 3. Let every member read one guideline in the portal
    $client->accessGrants->create([
        'grantee_type' => 'project_member',
        'organization_project_id' => $project->id,
        'resource_type' => 'guideline',
        'resource_id' => 'org_guide_xxx',
        'abilities' => ['view'],
    ]);

    // 4. Tag project
    $client->projects->addTag($project->id, 'enterprise');
    $client->projects->addTag($project->id, '2026');
    $client->projects->addTag($project->id, 'crm');

    echo "Project setup complete!\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Prospects](prospects.md) - Manage prospects within projects
- [Organization Users](users.md) - Team members
- [Portal Access](portal-access.md) - Project roles, access grants and resource bundles
- [Reports](reports.md) - Reports accessible in projects
- [Tasks](tasks.md) - Work tracked on a board, optionally within a project
