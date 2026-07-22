<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class MiscService extends AbstractService
{
    protected const string RESOURCE = 'misc';

    public function calculateTaxTotal(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/calculate-tax-total'), $params, $opts);
    }

    public function calculateCurrencyConversion(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/calculate-currency-conversion'), $params, $opts);
    }

    public function calculateDueDate(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/calculate-due-date'), $params, $opts);
    }

    public function determineTaxRateId(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/determine-tax-rate-id'), $params, $opts);
    }

    /**
     * Determine whether tax is charged for a sale, given the receiver context.
     *
     * Accepts `organization_receiver_user_id`, or an ad-hoc receiver via
     * `country_code` / `is_business_entity` / `is_eu_vat_registered` (all optional).
     * Returns `{ is_tax_charged: bool, reason: string, needs_attention: bool }` —
     * `reason` is a {@see \Enlivy\Enums\Tax\TaxApplicabilityReasons} value.
     */
    public function determineIsTaxCharged(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/determine-is-tax-charged'), $params, $opts);
    }

    public function testEmail(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/test-email'), $params, $opts);
    }

    public function translate(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/translate'), $params, $opts);
    }

    /**
     * Tax-threshold monitors (registration/nexus thresholds) for the
     * organization. Returns a raw snapshot.
     */
    public function taxMonitors(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/tax-monitors'), $params, $opts);
    }
}
