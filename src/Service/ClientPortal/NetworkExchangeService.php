<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\InvoiceNetworkExchange;
use Enlivy\Util\RequestOptions;

class NetworkExchangeService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = InvoiceNetworkExchange::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'network-exchanges'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "network-exchanges/{$id}"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "network-exchanges/{$id}/download"), $params, $opts);
    }

    public function downloadSignature(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "network-exchanges/{$id}/download-signature"), $params, $opts);
    }

    public function downloadPdf(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "network-exchanges/{$id}/download-pdf"), $params, $opts);
    }
}
