# Upgrading

What may need a change on your side in the last three releases, newest first. The notes for
3.2.0 are in [UPGRADING.md at 3.4.0](https://github.com/enlivy/enlivy-php/blob/3.4.0/UPGRADING.md),
3.1.0 in [UPGRADING.md at 3.3.0](https://github.com/enlivy/enlivy-php/blob/3.3.0/UPGRADING.md),
and older versions in [UPGRADING.md at 3.2.0](https://github.com/enlivy/enlivy-php/blob/3.2.0/UPGRADING.md).

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

## 3.3.0

The API rebuilt tasks. Tasks created before the rebuild were removed, so recreate the ones you
still need; your task statuses were kept, as task stages.

| Before | After |
|---|---|
| `$client->taskStatuses`, `Organization\TaskStatus` | `$client->taskStages`, `Organization\TaskStage` |
| stage `can_be_completed` | `stage_type` (`Enums\Task\TaskStatuses`) |
| `organization_task_status_id`, include `organization_task_status` | `organization_task_stage_id`, `organization_task_stage` |
| `title_lang_map`, `content_lang_map` | `title`, `content`, plain text |
| `assigned_to_organization_user_id`, `assigned_by_organization_user_id` and their includes | write `assignee_organization_user_ids`; read the `organization_task_participants` include |
| filter `assigned_to_organization_user_id` | `assignee_organization_user_id` |
| `order`, `tasks->reorder()` | `board_rank`, `tasks->moveOnBoard()` |
| `EntityManifest::TASK_STATUS` | `TASK_STAGE` |
| the report fields and includes, filters `assigned_by_organization_user_id` and `has_lang_map` | — |

- A task's `status` is the type of its stage and cannot be written: move the task, or call
  `complete()`, `cancel()` or `reopen()`.
- Existing statuses were typed by their position, so check each stage's `stage_type`.
- `tasks->delete()` returns the status envelope as an `EnlivyObject` instead of a `Task`.
- The `task_statuses.*` abilities are gone, since stages follow `tasks.*`, and the `tasks` feature
  pack is retired: tasks are free.
- Prospect `social_profiles` became `links`: rows with a `kind` (`Enums\WebLinkKinds`), a `url`
  and an optional `label`. Sending `links` replaces the list, and a `social_profiles` key is
  ignored.
- `is_widget_enabled` turns an inbox's widget off outright, and `widget_origins` applies only while
  `is_widget_origin_restricted` is on. Inboxes created through the API hold mail from unknown
  senders unless you send `'is_quarantine_enabled' => false`.
- API credential writes merge: keys you leave out are kept, and a key sent empty is removed.
- `misc->testEmail()` no longer needs `send_to`, and a failed check answers `422` with the checks
  under `metadata`.
