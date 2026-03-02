<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

class ProfileService extends AbstractPortalService
{
    public function retrieve(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, 'profile'), $params, $opts);
    }

    public function update(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->portalPath($orgId, 'profile'), $params, $opts);
    }

    public function billingReadiness(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, 'profile/billing-readiness'), $params, $opts);
    }
}
