# Upgrading

What may need a change on your side in the last three releases, newest first. The notes for
3.1.0 are in [UPGRADING.md at 3.3.0](https://github.com/enlivy/enlivy-php/blob/3.3.0/UPGRADING.md),
and older versions in [UPGRADING.md at 3.2.0](https://github.com/enlivy/enlivy-php/blob/3.2.0/UPGRADING.md).

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

## 3.2.0

| Before | After |
|---|---|
| `$client->payslipSchemas`, `Organization\PayslipSchema`, `Enums\Payslip\Fields`, the payslip's schema id, `information`, include and filter | the payslip's `lines`, now required on create |
| `Organization\ContractConnection` | `Organization\Connection` |
| `$client->projectPermissionProspects` | `$client->projectPermissionPipelines` |
| `$portal->prospects->board()` | `$portal->pipelines->board()` or `boardForProject()` |

- `contracts->connections()` keeps its name, but each row now carries `entity`, `liveness` and the
  record itself under `item`.
- Pipeline grants take `view_scope` and `edit_scope` instead of the `can_view_*` and `can_edit_*`
  booleans, and name a pipeline instead of a stage. A grant cannot be repointed: delete it and
  create another.
- The portal board's `meta.project_id` is now `meta.organization_project_id`.
- These now answer `422`: accepting a proposal without `payment_method_kind` when it needs one, a
  prefix `current_number` that is not a positive integer or would reissue a number, and a product
  naming a retired `organization_tax_class_id`.
- Payroll writes need the `payroll` feature pack, and answer `402` without it.
