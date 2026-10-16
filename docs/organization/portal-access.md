# Portal Access

Decide which report schemas, playbooks, guidelines and prospect pipelines each person reaches in the
customer portal. A portal session opens a module (see [Customer Portal](customer-portal.md)); access
grants decide what inside it a person sees and may do.

A grant names **who**, **where** and **what**, with the abilities it opens:

```
who    an organization role, a project role, every member of a project, or one person
where  one project, a resource bundle (wherever it is attached), or everywhere
what   one report schema, playbook, guideline or prospect pipeline, or all of one kind
```

Grants only add: a person holds everything any grant that reaches them opens.

## Granting access

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// Contractors on one project file and edit their own work reports for a day after submitting
$grant = $client->accessGrants->create([
    'grantee_type' => 'project_role',
    'grantee_id' => 'org_proj_role_xxx',
    'organization_project_id' => 'org_proj_xxx',
    'resource_type' => 'report_schema',
    'resource_id' => 'org_repschema_xxx',
    'abilities' => ['create', 'view_own', 'edit_own'],
    'settings' => ['edit_window_seconds' => 86400],
]);

// One person reads and downloads every playbook, everywhere
$client->accessGrants->create([
    'grantee_type' => 'person',
    'grantee_id' => 'org_user_xxx',
    'resource_type' => 'playbook',
    'abilities' => ['view', 'download'],
]);
```

| Field | Notes |
|-------|-------|
| `grantee_type` | `Enums\Access\GranteeTypes`: `user_role`, `project_role`, `project_member` or `person` |
| `grantee_id` | The organization role, project role or organization user; refused for `project_member` |
| `organization_project_id` | One project. `project_role` and `project_member` need a project or a bundle |
| `organization_resource_bundle_id` | A bundle instead of a project; not both |
| `resource_type` | `Enums\Access\ResourceTypes` |
| `resource_id` | One record of that type, or null for all of them |
| `abilities` | At least one, from the list for the resource type |
| `settings` | `edit_window_seconds`, for report schemas only: how long after submitting an author may still edit |

A prospect pipeline grant needs a project or a bundle, since prospects live in projects.

`update()` changes only `abilities` and `settings`. To point a grant at someone or something else,
delete it and create another.

### Abilities

| Resource type | Abilities | Presets |
|---------------|-----------|---------|
| `report_schema` | `create`, `view_own`, `view_others`, `edit_own` | file: `create`, `view_own`, `edit_own`; review: `view_others` |
| `playbook`, `guideline` | `view`, `edit`, `view_revisions`, `download` | read: `view`; read and download: `view`, `download`; edit: all four |
| `prospect_pipeline` | `view_own`, `view_unassigned`, `view_all`, `edit_own`, `edit_unassigned`, `edit_all`, `add`, `claim`, `reassign` | own; own and unassigned; all |

`$client->frontend->all()` lists the same under `access.resource_types`, with each preset's abilities and
names in the caller's language.

### Who may grant

Grants need the `access_grants.view`, `access_grants.manage` and `access_grants.delete` abilities
(OAuth scopes `team:read` and `team:write`). A grant on a module also needs the right to see that
module in the back office (`report_schemas.view`, `playbooks.view`, `guidelines.view` or
`prospects.view`); without it the API answers `422`.

### Listing and removing

```php
<?php

$grants = $client->accessGrants->list([
    'organization_project_id' => 'org_proj_xxx',
    'resource_type' => 'report_schema',
    'include' => ['grantee_project_role', 'grantee_person', 'grantee_user_role'],
]);

foreach ($grants as $grant) {
    echo "{$grant->grantee_type}: " . implode(', ', $grant->abilities) . "\n";
}

$client->accessGrants->delete('org_acc_grant_xxx');
```

Of the three grantee includes, only the one matching `grantee_type` carries a record; the others read
null. A delete answers a status, not the grant.

## Project roles

Each organization owns its project roles. It starts with four, aliased `team_member`, `contractor`,
`client` and `prospect`, and can rename them or add more. A project member holds at most one.

```php
<?php

$role = $client->projectRoles->create(['name' => 'Reviewer', 'alias' => 'reviewer']);

$client->projectMembers->create('org_proj_xxx', [
    'organization_user_id' => 'org_user_xxx',
    'organization_project_role_id' => $role->id,
]);

$reviewers = $client->projectMembers->list('org_proj_xxx', [
    'organization_project_role_id' => $role->id,
    'include' => ['organization_user', 'organization_project_role'],
]);
```

Deleting a project role deletes every grant that names it; its members stay in their projects without
a role.

## Resource bundles

A bundle groups grants so they can be applied in many places at once. Create grants with an
`organization_resource_bundle_id`, then attach the bundle to a project, or to the whole organization:

```php
<?php

$client->resourceBundleAttachments->create('org_res_bndl_xxx', [
    'organization_project_id' => 'org_proj_xxx',
]);

// Everywhere in the organization
$client->resourceBundleAttachments->create('org_res_bndl_xxx', [
    'organization_project_id' => null,
]);

// Where the bundle applies; an empty project id lists the organization-wide attachment
$attachments = $client->resourceBundleAttachments->list('org_res_bndl_xxx', [
    'organization_project_id' => '',
]);

$client->resourceBundleAttachments->delete('org_res_bndl_xxx', 'org_res_bndl_att_xxx');
```

`organization_project_id` must be sent, null included. Attaching where the bundle already applies
returns that attachment instead of a new one. A project's `resource_bundle_attachments` include lists
its attachments with the ids `delete()` needs. Attaching and detaching need
`resource_bundles.manage` and the `access_grants` abilities, since they hand out or withdraw the
bundle's grants.

## What a person holds

```php
<?php

$access = $client->organizationUsers->access('org_user_xxx');

if (!$access->is_current) {
    echo "A recent change is still being applied (built at {$access->built_at})\n";
}

foreach ($access->access as $row) {
    echo "{$row['resource_type']}.{$row['ability']}"
        . " in " . ($row['organization_project_id'] ?? 'every project')
        . " on " . ($row['resource_id'] ?? 'all') . "\n";
}
```

Removing access takes effect at once. Adding it is applied in the background, usually within
seconds, and `is_current` reads false until it lands. Reading a person's access needs `users.view`
and `access_grants.view`.

## In the portal

Portal lists of report schemas, playbooks and guidelines take `access_ability` (one ability of that
resource type) and `access_project_id`, to narrow to the records the reader holds that ability on,
inside one project. An empty `access_project_id` keeps the rights that reach everywhere:

```php
<?php

$fileable = $portal->reportSchemas->list([
    'access_ability' => 'create',
    'access_project_id' => 'org_proj_xxx',
]);
```

Each record then carries `access`, what the reader may do with it, and a portal project carries
`access`, the reader's grants inside it.

## Related

- [Customer Portal](customer-portal.md) - sessions and the modules they open
- [Projects](projects.md) - projects and their members
- [Reports](reports.md) - report schemas and reports
- [Prospects](prospects.md) - pipelines worked from the portal
