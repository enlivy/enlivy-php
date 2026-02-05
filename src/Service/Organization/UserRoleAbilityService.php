<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\UserRoleAbility;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing user role abilities.
 */
class UserRoleAbilityService extends AbstractService
{
    /**
     * @return Collection<UserRoleAbility>
     */
    public function list(string $roleId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "user-roles/{$roleId}/abilities"), $params, $opts);
    }

    public function sync(string $roleId, array $params, ?RequestOptions $opts = null): UserRoleAbility
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserRoleAbility */
        return $this->request('POST', $this->orgPath($orgId, "user-roles/{$roleId}/abilities"), $params, $opts);
    }

    public function delete(string $roleId, array $params = [], ?RequestOptions $opts = null): UserRoleAbility
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserRoleAbility */
        return $this->request('DELETE', $this->orgPath($orgId, "user-roles/{$roleId}/abilities"), $params, $opts);
    }
}
