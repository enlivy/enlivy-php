<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Util\RequestOptions;

/**
 * These endpoints answer with a plain list of ability rows, not a paginated
 * resource, so every method returns the raw payload — walk it with `toArray()`.
 * Abilities are addressed by name under `abilities`; there is no id to pass.
 */
class UserRoleAbilityService extends AbstractService
{
    use HasFilters;

    public const array AVAILABLE_FILTERS = [];

    /**
     * Reports what the role answers, not what it stores: a role with full
     * back-office access returns every ability, as entries whose `id` is null.
     */
    public function list(string $roleId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "user-roles/{$roleId}/abilities"), $params, $opts);
    }

    /**
     * Additive — names the role already holds are ignored. Rejected for a role
     * with full back-office access, which stores no rows of its own.
     */
    public function sync(string $roleId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "user-roles/{$roleId}/abilities"), $params, $opts);
    }

    public function delete(string $roleId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "user-roles/{$roleId}/abilities"), $params, $opts);
    }
}
