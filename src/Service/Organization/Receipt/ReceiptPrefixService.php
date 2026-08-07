<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Receipt;

use Enlivy\Collection;
use Enlivy\Organization\ReceiptPrefix;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * @method ReceiptPrefix restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReceiptPrefixService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'receipt-prefixes';
    protected const ?string RESOURCE_CLASS = ReceiptPrefix::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'receipts',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<ReceiptPrefix>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ReceiptPrefix> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ReceiptPrefix
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ReceiptPrefix */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ReceiptPrefix
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ReceiptPrefix */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): ReceiptPrefix
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ReceiptPrefix */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ReceiptPrefix
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ReceiptPrefix */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
