<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\EnlivyObject;
use Enlivy\Organization\WorkingTimeDay;
use Enlivy\Util\RequestOptions;

class WorkingTimeDayService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = WorkingTimeDay::class;

    public function month(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, 'working-time-days/month'), $params, $opts);
    }

    public function attestMonth(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, 'working-time-days/month/attest'), $params, $opts);
    }
}
