<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\TenantBilling;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for the tenant-billing 30-day trial flow (activation + mid-trial pack toggles).
 */
class TenantBillingTrialService extends AbstractService
{
    protected const string RESOURCE = 'tenant-billing/trial';

    /**
     * Activate the 30-day trial for the organization.
     */
    public function activate(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/activate'), $params, $opts);
    }

    /**
     * Add a feature pack to the active trial.
     */
    public function addPack(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/packs'), $params, $opts);
    }

    /**
     * Drop a feature pack from the active trial.
     */
    public function dropPack(string $packSlug, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/packs/{$packSlug}"), $params, $opts);
    }
}
