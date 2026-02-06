<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Project;

use Enlivy\Collection;
use Enlivy\Organization\ProjectMember;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing project members.
 */
class ProjectMemberService extends AbstractService
{
    use HasIncludes;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
        'organization_project',
        'organization_user',
    ];

    /**
     * @return Collection<ProjectMember>
     */
    public function list(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "projects/{$projectId}/members"), $params, $opts);
    }

    public function create(string $projectId, array $params, ?RequestOptions $opts = null): ProjectMember
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProjectMember */
        return $this->request('POST', $this->orgPath($orgId, "projects/{$projectId}/members"), $params, $opts);
    }

    public function retrieve(string $projectId, string $memberId, array $params = [], ?RequestOptions $opts = null): ProjectMember
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProjectMember */
        return $this->request('GET', $this->orgPath($orgId, "projects/{$projectId}/members/{$memberId}"), $params, $opts);
    }

    public function update(string $projectId, string $memberId, array $params, ?RequestOptions $opts = null): ProjectMember
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProjectMember */
        return $this->request('PUT', $this->orgPath($orgId, "projects/{$projectId}/members/{$memberId}"), $params, $opts);
    }

    public function delete(string $projectId, string $memberId, array $params = [], ?RequestOptions $opts = null): ProjectMember
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProjectMember */
        return $this->request('DELETE', $this->orgPath($orgId, "projects/{$projectId}/members/{$memberId}"), $params, $opts);
    }
}
