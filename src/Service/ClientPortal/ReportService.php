<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Report;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ReportService extends AbstractPortalService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = Report::class;

    public const array AVAILABLE_INCLUDES = [
        'report_schema',
        'organization_user',
        'organization_project',
        'recipient_organization_user',
        'links',
        'schema_snapshot',
        'report_schema.report_schema_fields',
    ];

    public function overview(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, 'reports/overview'), $params, $opts);
    }

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'reports'), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('POST', $this->portalPath($orgId, 'reports'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('GET', $this->portalPath($orgId, "reports/{$id}"), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('PUT', $this->portalPath($orgId, "reports/{$id}"), $params, $opts);
    }

    /**
     * Only the report's recipient acknowledges it, once it is submitted under a schema that asks for
     * acknowledgement; `can_acknowledge` on the report says whether the reader may.
     */
    public function acknowledge(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('POST', $this->portalPath($orgId, "reports/{$id}/acknowledge"), $params, $opts);
    }

    /**
     * The report's PDF, for a reader whose access opens it. `layout` is `standard` or `compact`,
     * `locale` a language code or `auto`.
     */
    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "reports/{$id}/download"), $params, $opts);
    }
}
