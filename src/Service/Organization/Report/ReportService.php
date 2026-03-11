<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Report;

use Enlivy\Collection;
use Enlivy\Organization\Report;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing reports.
 *
 * @method Report restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReportService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'reports';
    protected const ?string RESOURCE_CLASS = Report::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'report_schema',
        'organization_user',
        'organization_user_role',
        'deleted_by_user',
        'organization_project',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_report_schema_id',
        'organization_user_id',
        'organization_user_role_id',
        'organization_project_id',
        'report_date_from',
        'report_date_to',
    ];

    /**
     * List all reports.
     *
     * Resource-specific filters:
     * - `report_date_from` / `report_date_to` (date, Y-m-d) - Report date range
     *
     * @return Collection<Report>
     *
     * @see HasFilters::GLOBAL_FILTERS for global filters (q, ids, page, per_page, etc.)
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
