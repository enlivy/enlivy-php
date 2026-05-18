<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\TenantBilling;

use Enlivy\Collection;
use Enlivy\Organization\Invoice;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for reading the organization's Enlivy-subscription invoices (tenant-billing).
 *
 * These invoices are issued by Enlivy SRL; the organization can view and download them
 * but cannot mutate them here.
 */
class TenantBillingInvoiceService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tenant-billing/invoices';
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
        'party_locales',
        'tag_ids',
        'taxes',
        'last_peppol_exchange',
        'contract',
        'charge_logs',
        'latest_charge_log',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
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

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download"), $params, $opts);
    }
}
