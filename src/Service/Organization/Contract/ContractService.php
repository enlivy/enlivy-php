<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Contract;

use Enlivy\Collection;
use Enlivy\Organization\Contract;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing contracts.
 *
 * @method Contract restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ContractService extends AbstractService
{
    use HasRestore;
    use HasDownload;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'contracts';
    protected const ?string RESOURCE_CLASS = Contract::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'parent_contract',
        'sender_user',
        'receiver_user',
        'file',
        'contract_status',
        'deleted_by_user',
        'contract_chapters',
        'contract_parties',
        'contract_prefix',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_contract_status_id',
        'organization_receiver_user_id',
        'organization_sender_user_id',
        'organization_user_id',
        'parent_organization_contract_id',
        'category',
        'source',
        'issued_at_from',
        'issued_at_to',
    ];

    /**
     * List all contracts.
     *
     * Resource-specific filters:
     * - `organization_contract_status_id` (string) - Filter by contract status
     * - `organization_receiver_user_id` (string) - Filter by receiver user
     * - `organization_sender_user_id` (string) - Filter by sender user
     * - `parent_organization_contract_id` (string) - Filter by parent contract
     * - `category` (string: core|amendment|addenda|supplement) - Contract category
     * - `source` (string: uploaded|internal) - Contract source
     * - `issued_at_from` / `issued_at_to` (datetime) - Issued date range
     *
     * @return Collection<Contract>
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

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Contract
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Contract
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Contract
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Contract
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function downloadEvidence(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download-evidence"), $params, $opts);
    }
}
