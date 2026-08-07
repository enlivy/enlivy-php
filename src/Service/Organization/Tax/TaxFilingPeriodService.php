<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Tax;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\TaxFilingPeriod;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * @method TaxFilingPeriod restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class TaxFilingPeriodService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tax-filing-periods';
    protected const ?string RESOURCE_CLASS = TaxFilingPeriod::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'tax_filing_jurisdiction',
        'tax_type',
        'payments',
        'tax_registration',
    ];

    public const array AVAILABLE_FILTERS = [
        'status',
        'organization_tax_filing_jurisdiction_id',
        'organization_tax_type_id',
        'period_start_from',
        'period_start_to',
        'period_end_from',
        'period_end_to',
        'is_liability_cleared',
    ];

    /**
     * @return Collection<TaxFilingPeriod>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaxFilingPeriod> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): TaxFilingPeriod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxFilingPeriod */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): TaxFilingPeriod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxFilingPeriod */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): TaxFilingPeriod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxFilingPeriod */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): TaxFilingPeriod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxFilingPeriod */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Adopt the server-computed figures as the declared figures. Only valid
     * while the period is open.
     */
    public function acceptComputed(string $id, array $params = [], ?RequestOptions $opts = null): TaxFilingPeriod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxFilingPeriod */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/accept-computed"), $params, $opts);
    }

    /**
     * The mapped tax-return box figures for the period. Returns a raw box map,
     * not a filing-period resource. Pass `return_key` to select a specific
     * return when the jurisdiction has more than one.
     */
    public function returnView(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/return"), $params, $opts, EnlivyObject::class);
    }
}
