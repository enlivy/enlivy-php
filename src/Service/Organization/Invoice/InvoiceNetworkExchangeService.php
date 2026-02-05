<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\InvoiceNetworkExchange;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing invoice network exchanges.
 *
 * @method InvoiceNetworkExchange restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class InvoiceNetworkExchangeService extends AbstractService
{
    use HasRestore;
    use HasTagging;

    protected const string RESOURCE = 'invoices/network-exchanges';

    /**
     * @return Collection<InvoiceNetworkExchange>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoiceNetworkExchange */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoiceNetworkExchange */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoiceNetworkExchange */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoiceNetworkExchange */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function pull(string $institutionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$institutionId}/pull"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download"), $params, $opts);
    }

    public function downloadPdf(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download-pdf"), $params, $opts);
    }

    public function downloadSignature(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download-signature"), $params, $opts);
    }

    public function information(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/information"), $params, $opts);
    }
}
