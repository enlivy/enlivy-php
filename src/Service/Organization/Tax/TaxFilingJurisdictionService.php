<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Tax;

use Enlivy\Collection;
use Enlivy\Organization\TaxFilingJurisdiction;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing tax filing jurisdictions.
 */
class TaxFilingJurisdictionService extends AbstractService
{
    protected const string RESOURCE = 'tax-filing-jurisdictions';

    /**
     * @return Collection<TaxFilingJurisdiction>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): TaxFilingJurisdiction
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingJurisdiction */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): TaxFilingJurisdiction
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingJurisdiction */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): TaxFilingJurisdiction
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var TaxFilingJurisdiction */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
