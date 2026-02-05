<?php

declare(strict_types=1);

namespace Enlivy\Service\Project;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class ProjectPermissionProspectService extends AbstractService
{
    public function list(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "projects/{$projectId}/permission-prospects"), $params, $opts);
    }

    public function create(string $projectId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "projects/{$projectId}/permission-prospects"), $params, $opts);
    }

    public function retrieve(string $projectId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "projects/{$projectId}/permission-prospects/{$permissionId}"), $params, $opts);
    }

    public function update(string $projectId, string $permissionId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, "projects/{$projectId}/permission-prospects/{$permissionId}"), $params, $opts);
    }

    public function delete(string $projectId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "projects/{$projectId}/permission-prospects/{$permissionId}"), $params, $opts);
    }

    public function storeBody(string $projectId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "projects/{$projectId}/permission-prospects"), $params, $opts);
    }
}
