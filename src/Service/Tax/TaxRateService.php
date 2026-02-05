<?php

declare(strict_types=1);

namespace Enlivy\Service\Tax;

use Enlivy\Collection;
use Enlivy\TaxRate;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing tax rates.
 *
 * @method TaxRate restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class TaxRateService extends AbstractService
{
    use HasRestore;

    protected const string RESOURCE = 'tax-rates';

    /**
     * @return Collection<TaxRate>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): TaxRate
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxRate */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): TaxRate
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxRate */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): TaxRate
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxRate */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): TaxRate
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxRate */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
