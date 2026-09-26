# Tasks

Your organization's own work: tasks on a board of typed stages, with assignees and followers,
links to invoices, prospects and other tasks, a comment thread, and email reminders. Tasks are free
for every organization.

Tasks were rebuilt in 3.3.0 — task statuses became task stages and the task shape changed. Coming
from an earlier version, read [UPGRADING](../../UPGRADING.md) first.

## Key concepts

### A stage's type is the task's status

A stage carries a `stage_type`, and every task on it takes that type as its `status`
(`Enums\Task\TaskStatuses`):

| status | open? |
|---|---|
| `not_started` | yes |
| `in_progress` | yes |
| `waiting` | yes |
| `completed` | no |
| `cancelled` | no |

There is no status field to write. A task changes status by moving: update its
`organization_task_stage_id`, place it with `moveOnBoard()`, or use `complete()`, `cancel()` and
`reopen()`. Moving onto a `cancelled` stage cancels the task; onto a `completed` stage completes it.

A new task starts on the stage you name, which must be open; name none and it lands on the first
`not_started` stage. An open task that ends up with no stage waits in the board's first column
until somebody places it.

### One level of subtasks

A task with a `parent_organization_task_id` is a subtask. Subtasks cannot have subtasks of their
own, never sit on a stage, and carry their parent's project. A parent holds up to 100 and counts
them in `children_count`, `open_children_count` and `completed_children_count`.

Deleting a task deletes its subtasks, and restoring it brings back the ones deleted with it.
Cancelling a task cancels its open subtasks with the reason `parent_cancelled`.

## Creating a task

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$task = $client->tasks->create([
    'title' => 'Chase the March invoice',
    'content' => 'They asked for a copy with the PO number on it.',
    'due_at' => '2026-10-02T09:00:00Z',
    'organization_task_stage_id' => 'org_task_stat_todo',
    'assignee_organization_user_ids' => ['org_user_xxx'],
    'organization_invoice_ids' => ['org_inv_xxx'],
    'organization_prospect_ids' => [],
    'related_organization_task_ids' => ['org_task_other'],
]);

echo "{$task->title}: {$task->status}\n"; // not_started
```

`title` is plain text of up to 255 characters and `content` up to 65,000. Each people or link list
you send — `assignee_organization_user_ids` (up to 10), and `organization_invoice_ids`,
`organization_prospect_ids` and `related_organization_task_ids` (20 links in all across the three,
counting links other tasks made to this one) — replaces that list whole on `update()`. A list you
leave out is left alone, and an assignee you drop stays on as a follower.

Only people who can see tasks can be assigned or mentioned, and only someone who can open an
invoice or a prospect may link one. A task cannot be related to itself, its parent or its
subtasks, since those are related already.

## Listing tasks

```php
<?php

$tasks = $client->tasks->list([
    'status' => ['not_started', 'in_progress'],
    'assignee_organization_user_id' => 'org_user_xxx',
    'due_at_to' => '2026-10-31T23:59:59Z',
    'include' => ['organization_task_stage', 'organization_task_participants'],
    'include_meta' => 'navigation',
]);

foreach ($tasks as $task) {
    echo "{$task->title} — {$task->status}, due {$task->due_at}\n";

    foreach ($task->organization_task_participants->data ?? [] as $participant) {
        echo "  {$participant->role}: {$participant->organization_user_id}\n";
    }
}
```

Filters worth knowing:

| filter | reads |
|---|---|
| `status` | one or more statuses |
| `organization_project_id` / `without_project` | tasks in a project, or in none |
| `assignee_organization_user_id` | tasks assigned to someone |
| `participant_organization_user_id` | tasks someone is on, in any role |
| `organization_invoice_id`, `organization_prospect_id`, `related_organization_task_id` | tasks linked to a record |
| `parent_organization_task_id` / `is_subtask` | subtasks of a task, or only subtasks or only top-level tasks |
| `unplaced` | open top-level tasks with no stage — the board's first column |
| `due_at_from`, `due_at_to`, `completed_at_from` | date windows; `completed_at_from` keeps every task that is not completed |

The reverse view is an include: invoices and prospects take `organization_tasks`.

```php
$invoice = $client->invoices->retrieve('org_inv_xxx', ['include' => 'organization_tasks']);

foreach ($invoice->organization_tasks->data ?? [] as $task) {
    echo "{$task->title} ({$task->status})\n";
}
```

Linked records answer to the reader's own rights: `organization_invoices`, `organization_prospects`
and an invoice's or prospect's `organization_tasks` read null for someone who may not view that
kind of record.

## The board

`board()` answers one row per column: every stage in order, led by a column for open tasks that
have no stage whenever there are any. Each column carries its first 20 cards, its `total_count`
and whether it `has_more`. `include` names what each card carries. The board takes `q` and the
list's filters except `title`, `content`, `organization_task_stage_id`,
`parent_organization_task_id`, `is_subtask` and `unplaced`, which it ignores.

```php
<?php

$board = $client->tasks->board([
    'assignee_organization_user_id' => 'org_user_xxx',
    'include' => 'organization_task_participants',
]);

foreach ($board as $column) {
    $stage = $column->organization_task_stage?->data;
    echo ($stage ? $stage->title_lang_map['en'] : 'Waiting for a stage') . " ({$column->total_count})\n";

    foreach ($column->tasks->data as $card) {
        echo "  - {$card->title}\n";
    }
}
```

A column's later cards come from `list()` with the same filters, the column's stage (or
`unplaced`), and `order_by=board`, so the pages join up with what the board showed:

```php
$more = $client->tasks->list([
    'organization_task_stage_id' => 'org_task_stat_doing',
    'assignee_organization_user_id' => 'org_user_xxx',
    'order_by' => 'board',
    'page' => 2,
    'limit' => 20,
]);
```

## Moving and closing a task

```php
<?php

// Between two cards on a stage…
$client->tasks->moveOnBoard('org_task_xxx', [
    'organization_task_stage_id' => 'org_task_stat_doing',
    'previous_organization_task_id' => 'org_task_above',
    'next_organization_task_id' => 'org_task_below',
]);

// …or at either end of it
$client->tasks->moveOnBoard('org_task_xxx', [
    'organization_task_stage_id' => 'org_task_stat_doing',
    'place' => 'top',
]);

$client->tasks->complete('org_task_xxx');
$client->tasks->cancel('org_task_xxx', ['cancel_reason' => 'duplicate']);
$client->tasks->reopen('org_task_xxx', ['organization_task_stage_id' => 'org_task_stat_doing']);
```

Cards sort by `board_rank`, then newest first for cards nobody has placed; a rank is lowercase
letters and digits, so a plain string comparison matches the server. Neighbours named out of that
order, on another stage, or naming the card itself are refused with `422`.

A person may cancel with `no_longer_needed` or `duplicate`; `parent_cancelled` is written by the
API. `reopen()` without a stage puts the task on the first `not_started` stage. An assignee may
complete and reopen their own task without being able to manage everyone's.

## Comments and the feed

A comment has an author, so the key must belong to a member of the organization. Mentioning
someone makes them a participant, unless they have unfollowed the task. Only the author edits a
comment, and the edit is stamped in `edited_at`; the author or whoever manages tasks can remove one.

```php
<?php

$comment = $client->taskComments->create('org_task_xxx', [
    'body' => 'The client asked for Friday.',
    'mentioned_organization_user_ids' => ['org_user_colleague'],
]);

$client->taskComments->update('org_task_xxx', $comment->id, ['body' => 'The client asked for Monday.']);
$client->taskComments->delete('org_task_xxx', $comment->id);
```

Comments are read through the feed, which interleaves them with the task's history, oldest first.
It pages by cursor rather than by page:

```php
$cursor = null;

do {
    $page = $client->tasks->feed('org_task_xxx', array_filter(['cursor' => $cursor, 'limit' => 50]));

    foreach ($page as $entry) {
        if ($entry->kind === 'comment') {
            echo "{$entry->occurred_at}: " . ($entry->comment->data->body ?? '(removed)') . "\n";
        } else {
            echo "{$entry->occurred_at}: {$entry->event->data->event_type}\n";
        }
    }

    $cursor = $page->getMeta()['next_cursor'] ?? null;
} while ($cursor !== null);
```

A removed comment keeps its place in the feed with a null `body`. Every entry already carries the
comment's author, or the event's changes and actor, so the feed needs no `include`.

The notifications sent about a task, and the task's own event trail, have their own readers:

```php
$notifications = $client->tasks->notifications('org_task_xxx', ['include' => 'subjects,sent_by_user']);
$history = $client->tasks->eventTrails(['subject_id' => 'org_task_xxx', 'include' => 'changes']);
```

## Following

The creator, the assignees, whoever assigned them, and anyone mentioned or commenting become
participants automatically, and `source` says how each first joined. Anyone who can see a task can
also follow it, or stop — except an assignee, who cannot leave a task they owe but can turn its
emails off:

```php
<?php

$me = $client->tasks->follow('org_task_xxx');

// Keep following without the emails
$client->tasks->follow('org_task_xxx', ['notifications_enabled' => false]);

$client->tasks->unfollow('org_task_xxx');
```

`follow()` answers with your participant row (`role`, `source`, `notifications_enabled`).

Commenting, following, and completing or reopening a task as its assignee are open to people who
only see tasks (cancelling needs the right to manage tasks), but an OAuth token also needs the
`workspace:write` scope to do them.

## Emails

An assignee is emailed when a task is assigned to them, unless they assigned it themselves or have
turned that task's emails off. When a task is completed, every participant with
`notifications_enabled` is emailed except whoever completed it, after a short pause and only if it
is still completed by then. Each assignee with something overdue, due that day, or linked only to
records that have closed gets a morning digest at 08:00 in their own time zone. Every email carries
a one-click opt-out for its kind; none of it needs the API.

## Stages

```php
<?php

$stage = $client->taskStages->create([
    'title_lang_map' => ['en' => 'Waiting on client', 'ro' => 'Așteaptă clientul'],
    'description_lang_map' => ['en' => 'Blocked until they answer'],
    'stage_type' => 'waiting',
    'rgba_color_code' => '140, 107, 0, 1',
]);

// Every stage, in the new order
$client->taskStages->reorder([
    'organization_task_stage_ids' => ['org_task_stat_todo', $stage->id, 'org_task_stat_done'],
]);

// A stage that still holds tasks names where they go: a stage of the same type
$client->taskStages->delete($stage->id, [
    'move_to_organization_task_stage_id' => 'org_task_stat_other_waiting',
]);

$client->taskStages->restore($stage->id);
```

`stage_type` defaults to `not_started`, and is locked while the stage holds any task, trashed ones
included. The last `not_started` or `completed` stage can be neither retyped nor deleted: new work
needs a stage to arrive on, and finished work one to go to. Deleting and restoring a stage answer
with a status envelope, not the stage.

## Related

- [Projects](projects.md) — tasks can belong to a project
- [Invoices](invoices.md) and [Prospects](prospects.md) — link tasks to the records they are about
- [Event Trails](event-trails.md) — the history the feed draws on
- [Enums](../enums.md) — `Enums\Task\*`
