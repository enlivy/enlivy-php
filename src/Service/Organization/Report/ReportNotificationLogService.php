<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Report;

use Enlivy\Collection;
use Enlivy\Organization\ReportNotificationLog;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * @method ReportNotificationLog restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReportNotificationLogService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'reports/notification-logs';
    protected const ?string RESOURCE_CLASS = ReportNotificationLog::class;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
        'report',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_report_id',
        'types',
        'created_at_from',
        'created_at_to',
    ];

    /**
     * @return Collection<ReportNotificationLog>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ReportNotificationLog> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ReportNotificationLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ReportNotificationLog */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ReportNotificationLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ReportNotificationLog */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
