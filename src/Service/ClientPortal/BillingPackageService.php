<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\BillingPackage;
use Enlivy\Util\RequestOptions;

class BillingPackageService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = BillingPackage::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'billing-packages'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BillingPackage
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "billing-packages/{$id}"), $params, $opts);
    }

    public function claim(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "billing-packages/{$id}/claim"), $params, $opts);
    }
}
