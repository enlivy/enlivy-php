<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Tax;

use Enlivy\Collection;
use Enlivy\Organization\Connection;
use Enlivy\Organization\TaxClass;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * @method TaxClass restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class TaxClassService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tax-classes';
    protected const ?string RESOURCE_CLASS = TaxClass::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'tax_rates_overview',
    ];

    public const array AVAILABLE_FILTERS = [
        'name',
        'description',
        'retired',
    ];

    /**
     * @return Collection<TaxClass>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaxClass> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): TaxClass
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxClass */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): TaxClass
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxClass */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): TaxClass
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxClass */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): TaxClass
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxClass */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * List every entity that still references this tax class.
     *
     * Each row carries the referencing entity under `item` plus its `liveness`. Facet counts per
     * entity live in the response `meta.connections`, reachable with `getMeta()`.
     *
     * Parameters:
     * - `entity` (array) - Narrow to these kinds: product, billing_schedule, proposal,
     *   billing_package, invoice, receipt
     * - `liveness` (string) - live, historical or trashed
     * - `limit` (int) - Page size
     * - `page` (int) - Page number
     *
     * @return Collection<Connection>
     */
    public function connections(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Connection> */
        return $this->requestCollection(
            'GET',
            $this->orgPath($orgId, self::RESOURCE . "/{$id}/connections"),
            $params,
            $opts,
            Connection::class,
        );
    }

    /**
     * Retire a tax class, keeping the documents that already name it.
     *
     * Refused while any live entity still references it; read `connections()` first to see what.
     */
    public function retire(string $id, array $params = [], ?RequestOptions $opts = null): TaxClass
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxClass */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/retire"), $params, $opts);
    }

    public function unretire(string $id, array $params = [], ?RequestOptions $opts = null): TaxClass
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxClass */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/unretire"), $params, $opts);
    }
}
