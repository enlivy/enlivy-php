<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Task;

use Enlivy\Collection;
use Enlivy\Organization\Task;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasReorder;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing tasks.
 *
 * @method Task restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class TaskService extends AbstractService
{
    use HasRestore;
    use HasReorder;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tasks';
    protected const ?string RESOURCE_CLASS = Task::class;

    public const array AVAILABLE_INCLUDES = [
        'assigned_by_organization_user',
        'assigned_to_organization_user',
        'completed_by_organization_user',
        'deleted_by_user',
        'organization',
        'parent_organization_task',
        'organization_project',
        'organization_task_status',
        'organization_report_schema',
        'organization_report',
        'tag_ids',
    ];

    public const array AVAILABLE_FILTERS = [
        'parent_organization_task_id',
        'organization_project_id',
        'assigned_by_organization_user_id',
        'assigned_to_organization_user_id',
        'title',
        'content',
        'has_lang_map',
    ];

    /**
     * @return Collection<Task>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Task
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
