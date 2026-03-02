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

    /**
     * List prospects in a project.
     */
    public function list(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, "projects/{$projectId}/prospects"), $params, $opts);
    }

    /**
     * Get the prospect board (kanban view) for a project.
     */
    public function board(string $projectId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "projects/{$projectId}/prospects/board"), $params, $opts);
    }

    /**
     * Create a prospect in a project.
     */
    public function create(string $projectId, array $params, ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "projects/{$projectId}/prospects"), $params, $opts);
    }

    /**
     * Retrieve a prospect in a project.
     */
    public function retrieve(string $projectId, string $id, array $params = [], ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$id}"), $params, $opts);
    }

    /**
     * Update a prospect in a project.
     */
    public function update(string $projectId, string $id, array $params, ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->portalPath($orgId, "projects/{$projectId}/prospects/{$id}"), $params, $opts);
    }
}
