<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Offer;
use Enlivy\Util\RequestOptions;

class OfferService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Offer::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'offers'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Offer
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "offers/{$id}"), $params, $opts);
    }

    public function claim(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "offers/{$id}/claim"), $params, $opts);
    }
}
