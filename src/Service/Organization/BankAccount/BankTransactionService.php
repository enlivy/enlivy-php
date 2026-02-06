<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\BankAccount;

use Enlivy\Collection;
use Enlivy\Organization\BankTransaction;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasImports;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing bank transactions.
 *
 * @method BankTransaction restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class BankTransactionService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasImports;
    use HasIncludes;

    protected const string RESOURCE = 'bank-transactions';
    protected const ?string RESOURCE_CLASS = BankTransaction::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'bank_account',
        'connection_entities',
        'cost_type',
        'deleted_by_user',
        'tag_ids',
    ];

    /**
     * @return Collection<BankTransaction>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BankTransaction
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): BankTransaction
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): BankTransaction
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): BankTransaction
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
