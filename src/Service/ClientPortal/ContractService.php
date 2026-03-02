<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\Contract;
use Enlivy\Util\RequestOptions;

class ContractService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Contract::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'contracts'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Contract
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "contracts/{$id}"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "contracts/{$id}/download"), $params, $opts);
    }
}
