<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Project;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * What one project member may do with one pipeline's prospects.
 *
 * `view_scope` and `edit_scope` take `own`, `own_and_unassigned` or `all`; view also refuses
 * `none`, edit may not exceed view, and `can_claim` needs a view scope that includes the
 * unassigned pool. The API refuses an incoherent grant, so these cannot be set independently.
 */
class ProjectPermissionPipelineService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_project',
        'organization_user',
        'organization_prospect_pipeline',
    ];

    public const array AVAILABLE_FILTERS = [];

    public function list(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "projects/{$projectId}/permission-pipelines"), $params, $opts);
    }

    /**
     * Grant a pipeline to a member named in the body.
     */
    public function create(string $projectId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "projects/{$projectId}/permission-pipelines"), $params, $opts);
    }

    /**
     * Grant a pipeline to a member named in the path, for callers that already hold the id.
     */
    public function createForUser(string $projectId, string $organizationUserId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "projects/{$projectId}/permission-pipelines/{$organizationUserId}"), $params, $opts);
    }

    public function retrieve(string $projectId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "projects/{$projectId}/permission-pipelines/{$permissionId}"), $params, $opts);
    }

    /**
     * A grant cannot be repointed to another member or another pipeline; delete and recreate.
     */
    public function update(string $projectId, string $permissionId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, "projects/{$projectId}/permission-pipelines/{$permissionId}"), $params, $opts);
    }

    public function delete(string $projectId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "projects/{$projectId}/permission-pipelines/{$permissionId}"), $params, $opts);
    }
}
