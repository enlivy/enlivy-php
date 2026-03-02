<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Report;
use Enlivy\Util\RequestOptions;

class ReportService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Report::class;

    public function overview(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, 'reports/overview'), $params, $opts);
    }

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'reports'), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Report
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, 'reports'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "reports/{$id}"), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Report
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->portalPath($orgId, "reports/{$id}"), $params, $opts);
    }
}
