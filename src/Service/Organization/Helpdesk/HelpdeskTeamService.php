<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\HelpdeskTeam;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * @method HelpdeskTeam restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class HelpdeskTeamService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;
    protected const string RESOURCE = 'helpdesk/teams';
    protected const ?string RESOURCE_CLASS = HelpdeskTeam::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'members',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_helpdesk_teammate_id',
    ];

    /**
     * @return Collection<HelpdeskTeam>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskTeam> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskTeam
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeam */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): HelpdeskTeam
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeam */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): HelpdeskTeam
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeam */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Replaces the whole member list: `members` is a list of `organization_helpdesk_teammate_id` and
     * `role` (`member` or `lead`); anyone left out leaves the team.
     */
    public function replaceMembers(string $id, array $params, ?RequestOptions $opts = null): HelpdeskTeam
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeam */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}/members"), $params, $opts);
    }

    /**
     * The desk answers a delete with a status envelope rather than the deleted row.
     */
    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts, EnlivyObject::class);
    }
}
