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
    ];

    /**
     * @return Collection<Invoice>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
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
