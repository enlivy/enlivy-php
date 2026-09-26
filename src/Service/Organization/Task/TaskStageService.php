<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Task;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\TaskStage;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasReorder;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * `stage_type` is locked while the stage holds any task, trashed ones included, and the last
 * `not_started` or `completed` stage can be neither retyped nor deleted.
 */
class TaskStageService extends AbstractService
{
    use HasRestore;
    use HasReorder;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'task-stages';
    protected const ?string RESOURCE_CLASS = TaskStage::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'title',
        'description',
    ];

    /**
     * @return Collection<TaskStage>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaskStage> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): TaskStage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaskStage */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): TaskStage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaskStage */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): TaskStage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaskStage */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * A stage that still holds tasks needs `move_to_organization_task_stage_id`, a stage of the
     * same type. Answers with a status envelope rather than the stage.
     */
    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts, EnlivyObject::class);
    }
}
