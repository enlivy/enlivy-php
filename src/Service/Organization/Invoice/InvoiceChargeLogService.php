<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\Organization\InvoiceChargeLog;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for reading invoice charge logs (audit trail of payment-collection attempts).
 *
 * Read-only: charge logs are written by the API's charge pipeline, not by clients.
 */
class InvoiceChargeLogService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'invoice-charge-logs';
    protected const ?string RESOURCE_CLASS = InvoiceChargeLog::class;

    public const array AVAILABLE_INCLUDES = [
        'invoice',
        'payment_method',
        'initiated_by_organization_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_invoice_id',
        'status',
    ];

    /**
     * List invoice charge logs.
     *
     * Resource-specific filters:
     * - `organization_invoice_id` (string) - Filter by invoice
     * - `status` (string) - Filter by charge log status (comma-separated)
     *
     * @return Collection<InvoiceChargeLog>
     *
     * @see HasFilters::GLOBAL_FILTERS for global filters (q, ids, page, per_page, etc.)
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<InvoiceChargeLog> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceChargeLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoiceChargeLog */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
