<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\Payslip;
use Enlivy\Util\RequestOptions;

class PayslipService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Payslip::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'payslips'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Payslip
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Payslip */
        return $this->request('GET', $this->portalPath($orgId, "payslips/{$id}"), $params, $opts);
    }
}
