<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Receipt;

use Enlivy\Collection;
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
 * @method Receipt restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReceiptService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasDownload;
    use HasEventTrails;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'receipts';
    protected const ?string RESOURCE_CLASS = Receipt::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'invoice',
        'receipt_prefix',
        'bank_account',
        'sender_user',
        'receiver_user',
        'deleted_by_user',
        'tag_ids',
        'taxes',
        'contract',
    ];

    public const array AVAILABLE_FILTERS = [
        'bank_account_id',
        'currency',
        'direction',
        'has_receiver_user',
        'has_sender_user',
        'organization_invoice_id',
        'organization_receiver_user_id',
        'organization_sender_user_id',
        'organization_user_id',
        'status',
        'total',
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
     * @return Collection<Receipt>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Receipt> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Receipt */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Receipt */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Receipt */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Receipt */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
