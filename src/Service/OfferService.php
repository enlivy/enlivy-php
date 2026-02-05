<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\Offer;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing offers.
 *
 * @method Offer restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class OfferService extends AbstractService
{
    use HasRestore;

    protected const string RESOURCE = 'offers';

    /**
     * @return Collection<Offer>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Offer
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Offer */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Offer
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Offer */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Offer
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Offer */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Offer
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Offer */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function expire(string $id, array $params = [], ?RequestOptions $opts = null): Offer
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Offer */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/expire"), $params, $opts);
    }
}
