<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\TenantBilling;

use Enlivy\EnlivyObject;
use Enlivy\Organization\TenantBilling;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for the tenant-billing system — how Enlivy charges a customer organization
 * for Feature Packs and Capacity Add-ons.
 */
class TenantBillingService extends AbstractService
{
    use HasIncludes;

    protected const string RESOURCE = 'tenant-billing';
    protected const ?string RESOURCE_CLASS = TenantBilling::class;

    public const array AVAILABLE_INCLUDES = [
        'billing_schedule',
        'credits',
        'usage_stats',
    ];

    /**
     * The catalog of available feature packs and capacity add-ons (pricing, caps, currencies).
     */
    public function catalog(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/catalog'), $params, $opts);
    }

    /**
     * The organization's current tenant-billing state (active packs/addons, trial, cycle).
     */
    public function state(array $params = [], ?RequestOptions $opts = null): TenantBilling
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TenantBilling */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/state'), $params, $opts);
    }

    /**
     * The Enlivy Subscription terms / tax structure applicable to this organization.
     */
    public function terms(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/terms'), $params, $opts);
    }

    /**
     * Current metered usage versus the caps granted by the active packs/addons.
     */
    public function usage(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/usage'), $params, $opts);
    }

    /**
     * Preview the price/proration impact of a pack/addon change without applying it.
     */
    public function preview(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/preview'), $params, $opts);
    }

    /**
     * Apply a pack/addon/billing-cycle change to the organization's subscription.
     */
    public function apply(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/apply'), $params, $opts);
    }
}
