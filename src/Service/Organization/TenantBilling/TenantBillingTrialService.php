<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\TenantBilling;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class TenantBillingTrialService extends AbstractService
{
    protected const string RESOURCE = 'tenant-billing/trial';

    /**
     * Apply a trial change set (add or drop feature packs in one atomic call).
     */
    public function applyChangeSet(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }
}
