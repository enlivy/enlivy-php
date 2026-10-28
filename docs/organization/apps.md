# Apps

An App is a service of its own that Enlivy shows: widgets on the dashboard and record pages, its own
pages and menu points, and records administered through Enlivy's screens. The App declares everything
in one bundle (a manifest and its presentation documents) and Enlivy draws it; no App code runs in
Enlivy. Building an App is in [Developer Apps](../developer-apps.md).

Installing and administering Apps is an organization setting done in the dashboard: browsing the
catalog, approving permissions, upgrading, suspending and removing an installation, its connections,
the widgets and pages it adds, and which roles hold its capabilities all answer a signed-in dashboard
session and no API credential. This page covers the two calls a developer's own tools make with an
OAuth token holding the `apps:develop` scope: installing their App where they manage services, and
asking an organization they belong to for an installation.

An App comes as `public` (installs anywhere, after review when it asks for a sensitive scope) or
`personal` (installs in the organizations its developer belongs to); `Enums\Apps\Distributions`.

## Installing your own App

Where the developer holds `organization.services`, their App installs directly. The grant is capped by
the token's scopes and the person's role in that organization.

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'access_token' => $accessToken, // an OAuth token holding apps:develop
    'organization_id' => 'org_xxx',
]);

$installation = $client->appInstallations->create([
    'user_app_id' => 'app_xxx',
    'user_app_revision_id' => 'app_rev_xxx',
    'permissions' => [
        'oauth_scopes' => ['accounting:read'],
        'connector_capabilities' => ['links.read', 'links.write'],
        'context_fields' => ['organization_user_id'],
    ],
    'capability_holders' => 'roles',
    'capability_holder_role_ids' => ['org_role_xxx'],
]);

echo $installation->status;       // active
echo $installation->connect_url;  // where a person connects their provider account
print_r($installation->adds);     // the pages and widgets the installed revision brings
```

`permissions` is the approved set: each of `oauth_scopes`, `connector_capabilities` and
`context_fields` is a list (empty when nothing is granted), and the App's required permissions must be
in it. `capability_holders` says who holds the App's capabilities: `admins` (the default), `everyone`,
or `roles` with `capability_holder_role_ids` (`Enums\Apps\CapabilityHolders`); owners and roles with
full access always hold them. A personal App that takes everything is installed with no `permissions`
key.

## Asking an organization to install it

Where the developer only belongs, they ask; an admin who manages Apps approves or declines in the
dashboard. One request waits per App and organization; a new publication moves it to the new revision,
and approval installs exactly the revision the request shows.

```php
<?php

$request = $client->appInstallRequests->create(['user_app_id' => 'app_xxx']);

echo $request->status;           // pending
echo $request->manifest_version; // the revision the organization is asked to install
```

What each organization answered is read from the developer's side:

```php
<?php

foreach ($client->userApps->installRequests('usr_xxx', 'app_xxx') as $request) {
    echo "{$request->organization_id}: {$request->status}\n"; // pending, approved, declined, cancelled
    echo $request->organization_app_installation_id ?? '';
}
```

Statuses are `Enums\Apps\InstallRequestStatuses`.

## What an organization sees of an App

The records an installed App leaves in an organization answer API keys as their own pages say:

- its webhook appears among the [event destinations](event-destinations.md) with `type` `app` and its
  `organization_app_installation_id`, showing no address and no secret, and its deliveries carry no
  `response` body;
- its event trail entries are listed under the installation and its connections (see
  [Event Trails](event-trails.md));
- every App installed, removed ones included, exports as the `app_access` data export
  (`Enums\ExportData\Types::APP_ACCESS`), with who published, installed and approved it and what it
  holds now.

## Field reference

### Installation

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | The installation |
| `user_app_id`, `user_app_revision_id` | string | The App and the installed revision |
| `app` | object | `key`, `name_lang_map`, `distribution`, `developer.name` |
| `manifest_revision`, `manifest_version` | int, string | Enlivy's revision number and the developer's version |
| `generation` | int | Counts every lifecycle change |
| `status` | string | `Enums\Apps\InstallationStatuses` |
| `granted_permissions`, `declared_permissions` | object | What was approved, and what the revision asks for |
| `capability_labels` | object | Each capability in the App's words |
| `connect_url` | string\|null | Where a person connects a provider account, while active |
| `adds` | object | `pages` and `widgets` the revision brings, each saying whether the reader reaches it |
| `upgrade` | object\|null | The published revision it can move to |
| `approved_at`, `suspended_at`, `removed_at` | string\|null | Lifecycle moments |

### Install request

| Field | Type | Description |
|-------|------|-------------|
| `id`, `organization_id`, `user_app_id`, `user_app_revision_id` | string | Who is asked to install what |
| `app` | object | `key`, `name_lang_map`, `distribution`, `developer.name` |
| `manifest_revision`, `manifest_version` | int, string | The revision asked for |
| `status` | string | `Enums\Apps\InstallRequestStatuses` |
| `requested_by_user_id`, `requested_by_name` | string | The developer |
| `declared_permissions`, `capability_labels` | object | What the revision asks for, in its words |
| `decided_by_user_id`, `decided_at` | string\|null | The admin's answer |
| `organization_app_installation_id` | string\|null | The installation an approval made |

## Enums

| Enum | Values |
|------|--------|
| `Apps\Distributions` | `public`, `personal` |
| `Apps\InstallationStatuses` | `active`, `suspended`, `removed` |
| `Apps\ConnectionStatuses` | `connected`, `disconnected`, `revoked` |
| `Apps\CapabilityHolders` | `everyone`, `admins`, `roles` |
| `Apps\InstallRequestStatuses` | `pending`, `approved`, `declined`, `cancelled` |
| `Apps\WidgetStatuses` | `ready`, `disconnected`, `suspended`, `unavailable` |
| `Apps\LifecycleEvents` | what an App's webhook is told about its installation: `app.installed`, `app.upgraded`, `app.permissions_changed`, `app.suspended`, `app.resumed`, `app.removed`, `app.switched_off`, `app.switched_on`, `connection.revoked` |
| `Apps\ProviderAccessRefusals` | why the App's service refused a request: `connection_disconnected`, `connection_revoked`, `installation_suspended`, `installation_removed`, `app_unavailable`, `permission_missing`, `forbidden` |
| `PageLayout\Pages` | the pages an App widget may sit on: `dashboard`, `user_view`, `helpdesk_overview`, `prospect_overview`, `helpdesk_conversation_view`, `report_view`, `invoice_view`, `app_overview` |

## Related

- [Developer Apps](../developer-apps.md): registering, revising and publishing an App
- [Event Destinations](event-destinations.md)
- [Errors](../errors.md)
