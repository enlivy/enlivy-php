# Upgrading

What may need a change on your side in the last three releases, newest first. The notes for
3.5.0 and 3.4.0 are in [UPGRADING.md at 4.1.0](https://github.com/enlivy/enlivy-php/blob/4.1.0/UPGRADING.md),
3.3.0 in [UPGRADING.md at 3.5.0](https://github.com/enlivy/enlivy-php/blob/3.5.0/UPGRADING.md),
3.2.0 in [UPGRADING.md at 3.4.0](https://github.com/enlivy/enlivy-php/blob/3.4.0/UPGRADING.md),
3.1.0 in [UPGRADING.md at 3.3.0](https://github.com/enlivy/enlivy-php/blob/3.3.0/UPGRADING.md),
and older versions in [UPGRADING.md at 3.2.0](https://github.com/enlivy/enlivy-php/blob/3.2.0/UPGRADING.md).

## 4.2.0

Nothing is removed. Three answers changed shape on the wire, and a refusal now says what to do.

| Before | After |
|---|---|
| `eventDestinations->retrieve()`, `list()` and `update()` answered `signing_secret` | null everywhere except the answers to `create()` and the new `rotateSecret()`; keep the secret when you create the destination, or rotate it |
| reports include `links` answered `[{type, id}]` | `[{entity, id, reference, title, status, meta, opens}]`, grouped by kind; `entity` replaces `type` |
| a refused organization call carried `meta.denied_by` and `meta.required_scopes` | `getRefusalCode()` is `route_denied` and `getFacts()` holds `denied_by`, `required_abilities`, `required_scopes`; `meta.denied_by` is still repeated for now |
| `Enums\Analytics\StandardStatuses`, frontend payload `analytics.standard_statuses` | `Enums\Analytics\SettlementStatuses` (same values), payload `analytics.settlement_statuses`; the old enum stays, deprecated |

- A `409` is one of two kinds: without a code (`isConflict()` true) the record changed since you read
  it, so reread and retry once; with a code, do what the code names first.
- Event destination `destination_url` is now `string|null` on the resource: an installed App's
  destination (`type` `app`) shows none. A webhook `destination_url` must be a public address.
- Event deliveries to an `app` destination carry no `response` body.
- Analytics sums carry a new `settlement_status` field beside `status`.

## 4.1.0

The SDK now refuses, before sending, the include and filter names the API rejects or ignores. A call
that used one of them already failed with a `422` or had no effect; it now throws
`InvalidArgumentException` locally.

| Before | After |
|---|---|
| `organizations` include `credits`, `remaining_credits`, `membership_features` | `tenant_billing`, `tenant_billing.credits`, `tenant_billing.usage_stats`, `tenant_billing.billing_schedule` |
| `invoiceNetworkExchanges->list()` / `update()` with `tax_mapping` or `recording_suggestions` | `retrieve()` |
| Any include on `guidelines` / `playbooks` revisions, `prospects->board()`, `prospectPipelines->board()`, `organizationUsers->activity()`, `bankAccounts->balance()`, `tenantBilling->state()` | none; those endpoints take no includes |
| `bankTransactions` filter `is_connected` | `state` (`backlog`, `completed`, `unbalanced`, `trashed`) |
| `invitationCodes` filter `user_id`; `projectMembers` and `projectProspectStages` filter `organization_project_id`; `userAddresses` filter `organization_user_id` | drop it; the id in the path already scopes the list |

## 4.0.0

Portal permissions moved into access grants. Your existing permissions, bundle links and member roles
were carried over by the API, so there is no data work; only code that called the old routes or read
the old fields changes. See [Portal Access](docs/organization/portal-access.md).

| Before | After |
|---|---|
| `$client->projectPermissionReports/Playbooks/Guidelines/Pipelines` | `$client->accessGrants` with `grantee_type`, `organization_project_id`, `resource_type` and `abilities` |
| `$client->resourceBundlePermissionReports/Playbooks/Guidelines` | `$client->accessGrants` with `organization_resource_bundle_id` |
| `projects->attachBundle($projectId, $bundleId)` | `resourceBundleAttachments->create($bundleId, ['organization_project_id' => $projectId])` |
| `projects->detachBundle()` | `resourceBundleAttachments->delete($bundleId, $attachmentId)`; the id is on the project include `resource_bundle_attachments` |
| project include `project_resource_bundles` | `resource_bundle_attachments` |
| member `member_role`, filter `member_role`, `Enums\Project\MemberRoles` | `organization_project_role_id`, a role from `$client->projectRoles` (aliases `team_member`, `contractor`, `client`, `prospect`) |
| report schema `type`, `Enums\ReportSchema\Types` | `kind`, `Enums\ReportSchema\Kinds` (`standup` and `end_of_day` became `daily_log`, `standard` became `general`) |
| portal `organization_user_abilities_by_project_id`, `allowed_for_organization_project_ids` | `access` on each record; list filters `access_ability` and `access_project_id` |
| portal project includes `available_report_schemas/playbooks/guidelines` | `access` |

- A pipeline grant's abilities replace `view_scope`, `edit_scope`, `can_add`, `can_claim` and
  `can_reassign`: `view_own`/`view_unassigned`/`view_all`, the same for `edit_`, and `add`, `claim`,
  `reassign`. Bundle priority is gone: grants only add.
- Creating a report schema needs `kind`. A report's kind decides whether `period_from`/`period_to` and
  `recipient_organization_user_id` are required or refused, so a write that sends them where the kind
  has no use for them now answers `422`.
- Reports filed before this release read `status` `submitted`.
- Assigning a role, or changing a role's abilities, answers `422` unless the caller holds every
  ability involved.
- The Reports pack caps report schemas: creating or restoring one over the cap answers `402`.
