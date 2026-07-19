<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
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

        /** @var BillingSchedule */
        return $this->request('GET', $this->portalPath($orgId, "billing-schedules/{$id}"), $params, $opts);
    }

    public function changePaymentMethod(string $id, array $params, ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BillingSchedule */
        return $this->request('PUT', $this->portalPath($orgId, "billing-schedules/{$id}/payment-method"), $params, $opts);
    }

    public function cancel(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BillingSchedule */
        return $this->request('POST', $this->portalPath($orgId, "billing-schedules/{$id}/cancel"), $params, $opts);
    }

    public function reconfigure(string $id, array $params, ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BillingSchedule */
        return $this->request('PUT', $this->portalPath($orgId, "billing-schedules/{$id}/reconfigure"), $params, $opts);
    }

    // Returns the proration/charge preview (not a billing schedule); apply with reconfigure().
    public function previewReconfigure(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "billing-schedules/{$id}/preview-reconfigure"), $params, $opts, EnlivyObject::class);
    }

    public function pause(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BillingSchedule */
        return $this->request('POST', $this->portalPath($orgId, "billing-schedules/{$id}/pause"), $params, $opts);
    }

    public function resume(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BillingSchedule */
        return $this->request('POST', $this->portalPath($orgId, "billing-schedules/{$id}/resume"), $params, $opts);
    }
}
