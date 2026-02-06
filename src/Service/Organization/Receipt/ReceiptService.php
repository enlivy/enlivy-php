<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Receipt;

use Enlivy\Collection;
use Enlivy\Organization\Receipt;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing receipts.
 *
 * @method Receipt restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReceiptService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasDownload;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'receipts';
    protected const ?string RESOURCE_CLASS = Receipt::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'invoice',
        'bank_account',
        'sender_user',
        'receiver_user',
        'deleted_by_user',
        'tag_ids',
        'taxes',
        'contract',
    ];

    public const array AVAILABLE_FILTERS = [
        'direction',
        'status',
        'organization_invoice_id',
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
     * List all receipts.
     *
     * Resource-specific filters:
     * - `direction` (string: inbound|outbound) - Receipt direction
     * - `status` (string: approval_required|draft|scheduled|pending|sent_email|sent_physical|payment_partial|paid|solved|overdue|cancelled)
     * - `organization_invoice_id` (string[]) - Filter by linked invoice IDs
     * - `paid_at_from` / `paid_at_to` (datetime) - Paid date range
     * - `issued_at_from` / `issued_at_to` (datetime) - Issued date range
     * - `created_at_from` / `created_at_to` (datetime) - Created date range
     * - `updated_at_from` / `updated_at_to` (datetime) - Updated date range
     *
     * @return Collection<Receipt>
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

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
