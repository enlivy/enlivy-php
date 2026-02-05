<?php

declare(strict_types=1);

namespace Enlivy\Service\Task;

use Enlivy\Collection;
use Enlivy\Task;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasReorder;
use Enlivy\Service\Concern\HasRestore;
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

    protected const string RESOURCE = 'tasks';

    /**
     * @return Collection<Task>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Task
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Task
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Task
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Task
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Task */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
