<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\BankAccount;

use Enlivy\Collection;
use Enlivy\Organization\BankTransactionCostType;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing bank transaction cost types.
 *
 * @method BankTransactionCostType restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class BankTransactionCostTypeService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'bank-transaction-cost-types';
    protected const ?string RESOURCE_CLASS = BankTransactionCostType::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'deleted_by_user',
        'tag_ids',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<BankTransactionCostType>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BankTransactionCostType
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): BankTransactionCostType
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): BankTransactionCostType
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): BankTransactionCostType
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
