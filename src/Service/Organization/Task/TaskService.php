<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Task;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Notification;
use Enlivy\Organization\Task;
use Enlivy\Organization\TaskBoardColumn;
use Enlivy\Organization\TaskFeedEntry;
use Enlivy\Organization\TaskParticipant;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasEventTrails;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * A task's status follows the type of the stage it sits on, so there is no status field to
 * write: move the task, or use `complete()`, `cancel()` and `reopen()`.
 *
 * @method Task restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class TaskService extends AbstractService
{
    use HasRestore;
    use HasEventTrails;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tasks';
    protected const ?string RESOURCE_CLASS = Task::class;

    public const array AVAILABLE_INCLUDES = [
        'created_by_organization_user',
        'completed_by_organization_user',
        'cancelled_by_organization_user',
        'deleted_by_user',
        'organization',
        'parent_organization_task',
        'organization_project',
        'organization_task_stage',
        'organization_task_participants',
        'organization_invoices',
        'organization_prospects',
        'related_organization_tasks',
    ];

    public const array AVAILABLE_FILTERS = [
        'title',
        'content',
        'status',
        'organization_task_stage_id',
        'parent_organization_task_id',
        'is_subtask',
        'unplaced',
        'organization_project_id',
        'without_project',
        'assignee_organization_user_id',
        'participant_organization_user_id',
        'created_by_organization_user_id',
        'due_at_from',
        'due_at_to',
        'completed_at_from',
        'organization_invoice_id',
        'organization_prospect_id',
        'related_organization_task_id',
    ];

    public const array PARTICIPANT_INCLUDES = [
        'organization_user',
        'assigned_by_organization_user',
    ];

    /**
     * @return Collection<Task>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Task> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Stages in board order, led by a column for open tasks with no stage when there are any. Page
     * a column through `list()` with the same filters, its stage (or `unplaced`) and `order_by=board`.
     *
     * @return Collection<TaskBoardColumn>
     */
    public function board(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaskBoardColumn> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . '/board'), $params, $opts, TaskBoardColumn::class);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Each id list sent, assignees or links, replaces that list whole.
     */
    public function update(string $id, array $params, ?RequestOptions $opts = null): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Takes the subtasks with it, and answers with a status envelope rather than the task.
     */
    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts, EnlivyObject::class);
    }

    public function complete(string $id, array $params = [], ?RequestOptions $opts = null): Task
    {
        return $this->action('POST', 'complete', $id, $params, $opts);
    }

    /**
     * `cancel_reason` is `no_longer_needed` or `duplicate`. Open subtasks are cancelled too, as
     * `parent_cancelled`.
     */
    public function cancel(string $id, array $params, ?RequestOptions $opts = null): Task
    {
        return $this->action('POST', 'cancel', $id, $params, $opts);
    }

    /**
     * Without `organization_task_stage_id` the task goes to the first `not_started` stage.
     */
    public function reopen(string $id, array $params = [], ?RequestOptions $opts = null): Task
    {
        return $this->action('POST', 'reopen', $id, $params, $opts);
    }

    /**
     * `organization_task_stage_id` is required, even within the same stage. Name the neighbours
     * the card lands between, or `place` it at the `top` or `bottom`.
     */
    public function moveOnBoard(string $id, array $params, ?RequestOptions $opts = null): Task
    {
        return $this->action('PUT', 'board-position', $id, $params, $opts);
    }

    /**
     * `notifications_enabled` switches the task's emails; it is how an assignee, who cannot
     * `unfollow()`, goes quiet. See `PARTICIPANT_INCLUDES`.
     */
    public function follow(string $id, array $params = [], ?RequestOptions $opts = null): TaskParticipant
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaskParticipant */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/follow"), $params, $opts, TaskParticipant::class);
    }

    public function unfollow(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}/follow"), $params, $opts, EnlivyObject::class);
    }

    /**
     * Comments and trail events, oldest first, each already carrying its author or its changes
     * and actor. Paged by cursor: pass `getMeta()['next_cursor']` back as `cursor`.
     *
     * @return Collection<TaskFeedEntry>
     */
    public function feed(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaskFeedEntry> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/feed"), $params, $opts, TaskFeedEntry::class);
    }

    /**
     * Takes `NotificationService::AVAILABLE_INCLUDES`.
     *
     * @return Collection<Notification>
     */
    public function notifications(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Notification> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/notifications"), $params, $opts, Notification::class);
    }

    private function action(string $method, string $verb, string $id, array $params, ?RequestOptions $opts): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request($method, $this->orgPath($orgId, self::RESOURCE . "/{$id}/{$verb}"), $params, $opts);
    }
}
