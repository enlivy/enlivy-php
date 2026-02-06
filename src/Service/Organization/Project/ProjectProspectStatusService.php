<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Project;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ProjectProspectStatusService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_project',
        'organization_prospect_status',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_project_id',
        'organization_prospect_status_id',
    ];

    public function list(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "projects/{$projectId}/prospect-statuses"), $params, $opts);
    }

    public function create(string $projectId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "projects/{$projectId}/prospect-statuses"), $params, $opts);
    }

    public function retrieve(string $projectId, string $statusId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "projects/{$projectId}/prospect-statuses/{$statusId}"), $params, $opts);
    }

    public function update(string $projectId, string $statusId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, "projects/{$projectId}/prospect-statuses/{$statusId}"), $params, $opts);
    }

    public function delete(string $projectId, string $statusId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "projects/{$projectId}/prospect-statuses/{$statusId}"), $params, $opts);
    }

    public function storeBody(string $projectId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "projects/{$projectId}/prospect-statuses"), $params, $opts);
    }
}
