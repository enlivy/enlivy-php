<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Project;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Project;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing projects.
 *
 * @method Project restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ProjectService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasIncludes;

    protected const string RESOURCE = 'projects';
    protected const ?string RESOURCE_CLASS = Project::class;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
        'tag_ids',
        'project_resource_bundles',
        'resource_bundles',
    ];

    /**
     * @return Collection<Project>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Project
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Project
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Project
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Project
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function attachBundle(string $projectId, string $bundleId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$projectId}/bundle/{$bundleId}"), $params, $opts);
    }

    public function detachBundle(string $projectId, string $bundleId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$projectId}/bundle/{$bundleId}"), $params, $opts);
    }
}
