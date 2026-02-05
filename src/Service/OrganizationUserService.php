<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\OrganizationUser;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing organization users.
 *
 * @method OrganizationUser restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class OrganizationUserService extends AbstractService
{
    use HasRestore;
    use HasTagging;

    protected const string RESOURCE = 'users';

    /**
     * @return Collection<OrganizationUser>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): OrganizationUser
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var OrganizationUser */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): OrganizationUser
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var OrganizationUser */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): OrganizationUser
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var OrganizationUser */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): OrganizationUser
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var OrganizationUser */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * @return Collection<EnlivyObject>
     */
    public function activity(string $userId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . "/{$userId}/activity"), $params, $opts);
    }

    public function reportsOverview(string $userId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$userId}/overview/reports"), $params, $opts);
    }
}
