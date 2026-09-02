<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\WorkingTime;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\WorkingTimeDay;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class WorkingTimeDayService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'working-time-days';
    protected const ?string RESOURCE_CLASS = WorkingTimeDay::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_employment',
        'organization_working_time_day_breaks',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_employment_id',
        'date_from',
        'date_to',
    ];

    /**
     * @return Collection<WorkingTimeDay>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<WorkingTimeDay> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function month(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/month'), $params, $opts);
    }

    public function upsertMonth(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . '/month'), $params, $opts);
    }

    public function attestMonth(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/month/attest'), $params, $opts);
    }
}
