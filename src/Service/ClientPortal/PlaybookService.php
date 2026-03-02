<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Playbook;
use Enlivy\Util\RequestOptions;

class PlaybookService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Playbook::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'playbooks'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Playbook
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "playbooks/{$id}"), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Playbook
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->portalPath($orgId, "playbooks/{$id}"), $params, $opts);
    }

    public function listRevisions(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, "playbooks/{$id}/revisions"), $params, $opts);
    }

    public function retrieveRevision(string $id, string $revisionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "playbooks/{$id}/revisions/{$revisionId}"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "playbooks/{$id}/download"), $params, $opts);
    }
}
