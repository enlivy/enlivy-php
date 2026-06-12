<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\BillingSchedule;
use Enlivy\Util\RequestOptions;

class BillingScheduleService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = BillingSchedule::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'billing-schedules'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "billing-schedules/{$id}"), $params, $opts);
    }

    public function changePaymentMethod(string $id, array $params, ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->portalPath($orgId, "billing-schedules/{$id}/payment-method"), $params, $opts);
    }

    public function cancel(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "billing-schedules/{$id}/cancel"), $params, $opts);
    }
}
