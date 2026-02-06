<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\InvoiceNetworkExchange;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
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
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'invoices/network-exchanges';
    protected const ?string RESOURCE_CLASS = InvoiceNetworkExchange::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'parsed_data',
        'invoice',
        'tag_ids',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_invoice_id',
        'organization_user_id',
        'invoice_state',
        'status',
    ];

    /**
     * List all invoice network exchanges.
     *
     * Resource-specific filters:
     * - `organization_invoice_id` (string) - Filter by invoice
     * - `invoice_state` (string: any|attached|unattached) - Filter by attachment state
     *
     * @return Collection<InvoiceNetworkExchange>
     *
     * @see HasFilters::GLOBAL_FILTERS for global filters (q, ids, page, per_page, etc.)
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNetworkExchange
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function pull(string $institutionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$institutionId}/pull"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download"), $params, $opts);
    }

    public function downloadPdf(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download-pdf"), $params, $opts);
    }

    public function downloadSignature(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download-signature"), $params, $opts);
    }

    public function information(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/information"), $params, $opts);
    }
}
