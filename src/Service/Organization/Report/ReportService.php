<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Report;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Report;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * `download()` answers the report's PDF; `layout` is `standard` or `compact` and `locale` a language
 * code or `auto`. A linked record prints its figures only to a reader who may open it.
 *
 * @method Report restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReportService extends AbstractService
{
    use HasRestore;
    use HasDownload;
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
        'organization_task',
        'recipient_organization_user',
        'acknowledged_by_organization_user',
        'links',
        'schema_snapshot',
        'author_portal_access',
        'report_schema.report_schema_fields',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_report_schema_id',
        'organization_user_id',
        'organization_user_role_id',
        'organization_project_id',
        'report_date_from',
        'report_date_to',
        'reported_by_organization_user_id',
        'organization_task_id',
        'recipient_organization_user_id',
        'kind',
        'status',
        'awaiting_acknowledgement',
        'linked_type',
        'linked_id',
    ];

    /**
     * @return Collection<Report>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Report> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Report */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Report */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Report */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Report */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Email a submitted report, with its PDF, to its recipient or to `send_to`, with up to ten `cc`.
     * A draft is refused. Each send is kept once in `reportNotificationLogs`.
     */
    public function email(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/email"), $params, $opts, EnlivyObject::class);
    }
}
