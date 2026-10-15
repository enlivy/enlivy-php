<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\Receipt;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ReceiptService extends AbstractPortalService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = Receipt::class;

    public const array AVAILABLE_INCLUDES = [
        'invoice',
        'bank_account',
        'sender_user',
        'receiver_user',
        'contract',
    ];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'receipts'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Receipt */
        return $this->request('GET', $this->portalPath($orgId, "receipts/{$id}"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "receipts/{$id}/download"), $params, $opts);
    }
}
