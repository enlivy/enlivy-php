<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Project;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\ProjectRole;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ProjectRoleService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'project-roles';
    protected const ?string RESOURCE_CLASS = ProjectRole::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
    ];

    public const array AVAILABLE_FILTERS = [
        'alias',
    ];

    /**
     * @return Collection<ProjectRole>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ProjectRole> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ProjectRole
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProjectRole */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ProjectRole
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProjectRole */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): ProjectRole
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProjectRole */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Also deletes every grant that names the role; its members stay in their projects without one.
     */
    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts, EnlivyObject::class);
    }
}
