<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\Exception\NotFoundException;
use Enlivy\EnlivyObject;
use Enlivy\Organization\HelpdeskTeammate;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Seats on the desk. A teammate is always an organization user who can sign in, and
 * creating one consumes a seat from the helpdesk pack.
 */
class HelpdeskTeammateService extends AbstractService
{
    use HasIncludes;
    use HasFilters;
    protected const string RESOURCE = 'helpdesk/teammates';
    protected const ?string RESOURCE_CLASS = HelpdeskTeammate::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_user',
        'owned_inboxes',
    ];

    public const array AVAILABLE_FILTERS = [
        'is_available',
        'organization_helpdesk_team_id',
    ];

    /**
     * @return Collection<HelpdeskTeammate>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskTeammate> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * The caller's own seat, or null when they have none.
     */
    public function me(array $params = [], ?RequestOptions $opts = null): ?HelpdeskTeammate
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        try {
            /** @var HelpdeskTeammate */
            return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/me'), $params, $opts);
        } catch (NotFoundException) {
            return null;
        }
    }

    /**
     * Saves the caller's own `notification_preferences`; open to anyone with a seat.
     */
    public function updateMe(array $params, ?RequestOptions $opts = null): HelpdeskTeammate
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeammate */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . '/me'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskTeammate
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeammate */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): HelpdeskTeammate
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeammate */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): HelpdeskTeammate
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskTeammate */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
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
