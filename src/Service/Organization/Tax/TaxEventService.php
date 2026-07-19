<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Tax;

use Enlivy\Collection;
use Enlivy\Organization\TaxEvent;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing tax events (the tax subledger).
 *
 * @method TaxEvent restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class TaxEventService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tax-events';
    protected const ?string RESOURCE_CLASS = TaxEvent::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'tax_filing_jurisdiction',
        'tax_type',
        'tax_filing_period',
    ];

    public const array AVAILABLE_FILTERS = [
        'direction',
        'source_type',
        'source_id',
        'category',
        'regime',
        'country_code',
        'organization_tax_filing_period_id',
        'is_reverse_charge',
        'tax_point_date_from',
        'tax_point_date_to',
    ];

    /**
     * @return Collection<TaxEvent>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaxEvent> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): TaxEvent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxEvent */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): TaxEvent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxEvent */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): TaxEvent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxEvent */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): TaxEvent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxEvent */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
