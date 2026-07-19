<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Tax;

use Enlivy\Collection;
use Enlivy\Organization\TaxFilingPeriodPayment;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing payments recorded against a tax filing period.
 *
 * Nested under a filing period: every method takes the filing-period id first.
 */
class TaxFilingPeriodPaymentService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'tax_filing_period',
        'bank_account',
    ];

    public const array AVAILABLE_FILTERS = [
        'status',
        'payment_type',
        'payment_date_from',
        'payment_date_to',
    ];

    /**
     * @return Collection<TaxFilingPeriodPayment>
     */
    public function list(string $periodId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaxFilingPeriodPayment> */
        return $this->requestCollection('GET', $this->orgPath($orgId, "tax-filing-periods/{$periodId}/payments"), $params, $opts);
    }

    public function create(string $periodId, array $params, ?RequestOptions $opts = null): TaxFilingPeriodPayment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingPeriodPayment */
        return $this->request('POST', $this->orgPath($orgId, "tax-filing-periods/{$periodId}/payments"), $params, $opts);
    }

    public function retrieve(string $periodId, string $paymentId, array $params = [], ?RequestOptions $opts = null): TaxFilingPeriodPayment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingPeriodPayment */
        return $this->request('GET', $this->orgPath($orgId, "tax-filing-periods/{$periodId}/payments/{$paymentId}"), $params, $opts);
    }

    public function update(string $periodId, string $paymentId, array $params, ?RequestOptions $opts = null): TaxFilingPeriodPayment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingPeriodPayment */
        return $this->request('PUT', $this->orgPath($orgId, "tax-filing-periods/{$periodId}/payments/{$paymentId}"), $params, $opts);
    }

    public function delete(string $periodId, string $paymentId, array $params = [], ?RequestOptions $opts = null): TaxFilingPeriodPayment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingPeriodPayment */
        return $this->request('DELETE', $this->orgPath($orgId, "tax-filing-periods/{$periodId}/payments/{$paymentId}"), $params, $opts);
    }

    public function restore(string $periodId, string $paymentId, array $params = [], ?RequestOptions $opts = null): TaxFilingPeriodPayment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingPeriodPayment */
        return $this->request('POST', $this->orgPath($orgId, "tax-filing-periods/{$periodId}/payments/restore/{$paymentId}"), $params, $opts);
    }
}
