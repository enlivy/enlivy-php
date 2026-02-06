<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Invoice;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing invoices.
 *
 * @method Invoice restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class InvoiceService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasDownload;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'invoices';
    protected const ?string RESOURCE_CLASS = Invoice::class;

    public const array AVAILABLE_INCLUDES = [
        'bank_account',
        'invoice_prefix',
        'sender_user',
        'receiver_user',
        'receiver_user_address',
        'line_items',
        'receipts',
        'deleted_by_user',
        'tag_ids',
        'taxes',
    ];

    public const array AVAILABLE_FILTERS = [
        'direction',
        'status',
        'is_downloadable',
        'is_tax_charged',
        'bank_account_id',
        'currency',
        'organization_receiver_user_id',
        'organization_sender_user_id',
        'organization_user_id',
        'source',
        'total',
        'product_ids',
        'network_exchange',
        'peppol_exchange_push_option',
        'peppol_exchange_pushed',
        'is_api_charge',
        'api_charged_organization_id',
        'paid_at_from',
        'paid_at_to',
        'issued_at_from',
        'issued_at_to',
        'created_at_from',
        'created_at_to',
        'updated_at_from',
        'updated_at_to',
    ];

    /**
     * List all invoices.
     *
     * Resource-specific filters:
     * - `direction` (string: inbound|outbound) - Invoice direction
     * - `status` (string: approval_required|draft|scheduled|pending|sent_email|sent_physical|payment_partial|paid|solved|overdue|cancelled)
     * - `is_downloadable` (bool) - Filter by downloadable invoices
     * - `is_tax_charged` (bool) - Filter by tax charged
     * - `paid_at_from` / `paid_at_to` (datetime) - Paid date range
     * - `issued_at_from` / `issued_at_to` (datetime) - Issued date range
     * - `created_at_from` / `created_at_to` (datetime) - Created date range
     * - `updated_at_from` / `updated_at_to` (datetime) - Updated date range
     *
     * @return Collection<Invoice>
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

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function email(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/email"), $params, $opts);
    }

    public function peppolPush(string $id, string $institution, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/peppol/{$institution}"), $params, $opts);
    }
}
