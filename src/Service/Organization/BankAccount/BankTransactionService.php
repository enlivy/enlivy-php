<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\BankAccount;

use Enlivy\Collection;
use Enlivy\Organization\BankTransaction;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasImports;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasFilters;
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
    use HasFilters;

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

    public const array AVAILABLE_FILTERS = [
        'state',
        'direction',
        'connection_entity_type',
        'connection_entity_id',
        'created_at_from',
        'created_at_to',
        'updated_at_from',
        'updated_at_to',
    ];

    /**
     * List all bank transactions.
     *
     * Resource-specific filters:
     * - `state` (string: backlog|classified|connected|connected_partially|danger|trashed)
     * - `direction` (string: inbound|outbound) - Transaction direction
     * - `connection_entity_type` (string: invoice|receipt|bank_transaction|user|payslip) - Required with connection_entity_id
     * - `connection_entity_id` (string) - Required with connection_entity_type
     * - `created_at_from` / `created_at_to` (datetime) - Created date range
     * - `updated_at_from` / `updated_at_to` (datetime) - Updated date range
     *
     * @return Collection<BankTransaction>
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
