<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\ProspectActivity;
use Enlivy\Util\RequestOptions;

/**
 * What a member recorded against a prospect: calls, meetings, notes.
 *
 * Only the person who wrote an activity may change it, and the rows the system writes when a
 * prospect changes stage or assignee are never editable.
 */
class ProspectActivityService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = ProspectActivity::class;

    /**
     * @return Collection<ProspectActivity>
     */
    public function list(string $projectId, string $prospectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ProspectActivity> */
        return $this->requestCollection('GET', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$prospectId}/activities"), $params, $opts);
    }

    public function create(string $projectId, string $prospectId, array $params, ?RequestOptions $opts = null): ProspectActivity
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProspectActivity */
        return $this->request('POST', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$prospectId}/activities"), $params, $opts);
    }

    public function update(string $projectId, string $prospectId, string $activityId, array $params, ?RequestOptions $opts = null): ProspectActivity
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ProspectActivity */
        return $this->request('PUT', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$prospectId}/activities/{$activityId}"), $params, $opts);
    }
}
