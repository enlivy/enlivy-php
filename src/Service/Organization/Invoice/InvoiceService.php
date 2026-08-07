<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Invoice;
use Enlivy\Organization\Receipt;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasEventTrails;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * @method Invoice restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class InvoiceService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasDownload;
    use HasEventTrails;
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
        'party_locales',
        'tag_ids',
        'taxes',
        'last_peppol_exchange',
        'contract',
        'charge_logs',
        'latest_charge_log',
        'reversal_invoices',
        'parent_invoice',
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
     * @return Collection<Invoice>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Invoice> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Invoice */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Invoice */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Invoice */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Invoice */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function email(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/email"), $params, $opts);
    }

    /**
     * Push an invoice to a tax-authority e-invoicing network. Pass an optional
     * `document_type_code` (`380` commercial invoice, `381` credit note) to
     * override the document type inferred from the invoice.
     */
    public function peppolPush(string $id, string $institution, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/peppol/{$institution}"), $params, $opts);
    }

    /**
     * Get an invoice's e-invoicing status on a tax-authority network, with a
     * filing preview when it has not yet been pushed. Accepts the same optional
     * `document_type_code` override as the push. ANAF only.
     */
    public function peppolStatus(string $id, string $institution, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/peppol/{$institution}"), $params, $opts);
    }

    /**
     * Charge the invoice off-session against a stored payment method.
     */
    public function charge(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Invoice */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/charge"), $params, $opts);
    }

    /**
     * Refund an issued invoice, generating a reversal (credit-note) invoice.
     * Omit `line_items` for a full refund; pass them to refund partially.
     */
    public function refund(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Invoice */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/refund"), $params, $opts);
    }

    /**
     * Issue a standard invoice from a proforma.
     */
    public function issueInvoice(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Invoice */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/issue-invoice"), $params, $opts);
    }

    /**
     * Issue a receipt for the invoice.
     */
    public function issueReceipt(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Receipt */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/issue-receipt"), $params, $opts, Receipt::class);
    }
}
