# Upgrading

What may need a change on your side in the last three releases, newest first. The notes for
3.3.0 are in [UPGRADING.md at 3.5.0](https://github.com/enlivy/enlivy-php/blob/3.5.0/UPGRADING.md),
3.2.0 in [UPGRADING.md at 3.4.0](https://github.com/enlivy/enlivy-php/blob/3.4.0/UPGRADING.md),
3.1.0 in [UPGRADING.md at 3.3.0](https://github.com/enlivy/enlivy-php/blob/3.3.0/UPGRADING.md),
and older versions in [UPGRADING.md at 3.2.0](https://github.com/enlivy/enlivy-php/blob/3.2.0/UPGRADING.md).

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

## 3.5.0

Signing in, starting an account and managing API keys, OAuth clients and OAuth consent happen in the
Enlivy app now. The API removed those routes, or accepts them only from a signed-in dashboard, so
the methods that called them are gone.

| Before | After |
|---|---|
| `authentication->login/register/forgotPassword/resetPassword` | sign in and register in the Enlivy app |
| `users->sendEmailVerificationToken()` and the other email and phone methods | managed in the Enlivy app |
| `$client->userTokens`, `authentication->listTokens/deleteToken` | create and revoke API keys in the Enlivy app |
| `$client->oauthClients`, `$client->oauthAuthorizations` | register clients and review consent in the Enlivy app |
| `User::$email_verification_sent_at`, `User::$remember_token` | — |

- `users->update()` accepts only `username`, `first_name`, `last_name`, `name`, `locale`,
  `country_code` and `timezone`, and a user's names may be null.
- `invitationCodes` and `userAddresses` `delete()`/`restore()` return an `EnlivyObject`: read
  `->status`, not resource fields.
- Read `access_token` and `refresh_token` from `oauthTokens->create()`. A rotated refresh token
  presented again more than a minute later revokes every token issued from it, so store the new one.
- On the customer portal, a proposal no longer carries the organization's own fields (see
  [Proposals](docs/organization/proposals.md)), a document's `sender_user` and `receiver_user` carry
  only `id`, `name`, `first_name`, `last_name` and `avatar_url`, and an include the portal does not
  offer throws before the request.
- Page size is `limit`; `per_page` was never read, so lists returned 30 rows.

## 3.4.0

The customer portal's proposal payment is one call now. The two routes it replaced are gone from
the API, so the old methods answered `404`.

| Before | After |
|---|---|
| `$portal->proposals->selectPaymentMethod($id, ['payment_method_kind' => …])` then `createPaymentIntent($id)` | `$portal->proposals->pay($id, ['payment_method_kind' => 'card'])` |

- `pay()` answers `payment_method_kind`, `payment_provider` and what Stripe.js confirms for a card,
  or the transfer instructions for a bank transfer. `confirmPayment()` is unchanged.
- `charge_result.next_action_url` is no longer a 3DS page to redirect to: it is a payment link the
  customer has already been emailed. Show it if they are with you; do not email it again.
- An OAuth access token can no longer mint personal access tokens or register OAuth clients.
