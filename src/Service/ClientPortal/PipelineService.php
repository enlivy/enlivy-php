<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\ProspectPipeline;
use Enlivy\Util\RequestOptions;

/**
 * The pipelines a portal member may work, and the board for each.
 *
 * What a member sees is decided by the grant they hold on that pipeline, so two people can open
 * the same board and be shown different prospects.
 */
class PipelineService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = ProspectPipeline::class;

    /**
     * Every pipeline the member can reach, across all their projects.
     *
     * @return Collection<ProspectPipeline>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ProspectPipeline> */
        return $this->requestCollection('GET', $this->portalPath($orgId, 'pipelines'), $params, $opts);
    }

    /**
     * @return Collection<ProspectPipeline>
     */
    public function listForProject(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ProspectPipeline> */
        return $this->requestCollection('GET', $this->portalPath($orgId, "projects/{$projectId}/pipelines"), $params, $opts);
    }

    /**
     * Columns of prospects by stage, with the pipeline's totals in `meta`.
     */
    public function board(string $pipelineId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "pipelines/{$pipelineId}/board"), $params, $opts);
    }

    public function boardForProject(string $projectId, string $pipelineId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "projects/{$projectId}/pipelines/{$pipelineId}/board"), $params, $opts);
    }
}
