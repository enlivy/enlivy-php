<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\BillingSchedule;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasImports;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing billing schedules.
 *
 * @method BillingSchedule restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class BillingScheduleService extends AbstractService
{
    use HasRestore;
    use HasImports;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'billing-schedules';
    protected const ?string RESOURCE_CLASS = BillingSchedule::class;

    public const array AVAILABLE_INCLUDES = [
        'sender_user',
        'receiver_user',
        'contract',
        'deleted_by_user',
        'payments',
    ];

    public const array AVAILABLE_FILTERS = [
        'status',
    ];

    /**
     * List all billing schedules.
     *
     * Resource-specific filters:
     * - `status` (string: pending|active|cancelled)
     * - `type` (string: payment_plan|subscription)
     * - `direction` (string: inbound|outbound)
     * - `organization_sender_user_id` (string) - Filter by sender user
     * - `organization_receiver_user_id` (string) - Filter by receiver user
     * - `organization_contract_id` (string) - Filter by contract
     * - `organization_bank_account_id` (string) - Filter by bank account
     * - `starts_at_from` / `starts_at_to` (datetime) - Start date range
     * - `ends_at_from` / `ends_at_to` (datetime) - End date range
     *
     * @return Collection<BillingSchedule>
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

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
