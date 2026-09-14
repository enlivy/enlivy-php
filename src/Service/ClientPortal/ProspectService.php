<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Prospect;
use Enlivy\Util\RequestOptions;

class ProspectService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Prospect::class;

    public function list(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, "projects/{$projectId}/prospects"), $params, $opts);
    }

    public function create(string $projectId, array $params, ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('POST', $this->portalPath($orgId, "projects/{$projectId}/prospects"), $params, $opts);
    }

    public function retrieve(string $projectId, string $id, array $params = [], ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('GET', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$id}"), $params, $opts);
    }

    public function update(string $projectId, string $id, array $params, ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('PUT', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$id}"), $params, $opts);
    }

    /**
     * Every prospect the member can reach, across all their projects.
     *
     * @return Collection<Prospect>
     */
    public function listAcrossProjects(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Prospect> */
        return $this->requestCollection('GET', $this->portalPath($orgId, 'prospects'), $params, $opts);
    }

    public function retrieveAcrossProjects(string $id, array $params = [], ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('GET', $this->portalPath($orgId, "prospects/{$id}"), $params, $opts);
    }

    /**
     * Move a prospect along a stage path, recording the activity that justifies the move.
     *
     * Takes a required `organization_prospect_stage_path_id` that leaves the prospect's current
     * stage, plus an optional `description`, `outcome` and `organization_report_id`.
     */
    public function advance(string $projectId, string $id, array $params, ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('POST', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$id}/advance"), $params, $opts);
    }
}
