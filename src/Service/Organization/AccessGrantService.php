<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\AccessGrant;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class AccessGrantService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'access-grants';
    protected const ?string RESOURCE_CLASS = AccessGrant::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_project',
        'organization_resource_bundle',
        'grantee_user_role',
        'grantee_project_role',
        'grantee_person',
    ];

    public const array AVAILABLE_FILTERS = [
        'grantee_type',
        'grantee_id',
        'organization_project_id',
        'organization_resource_bundle_id',
        'resource_type',
        'resource_id',
    ];

    /**
     * @return Collection<AccessGrant>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<AccessGrant> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): AccessGrant
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var AccessGrant */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): AccessGrant
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var AccessGrant */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Only `abilities` and `settings` change; delete and recreate a grant to point it elsewhere.
     */
    public function update(string $id, array $params, ?RequestOptions $opts = null): AccessGrant
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var AccessGrant */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts, EnlivyObject::class);
    }
}
