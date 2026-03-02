<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Prospect;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Prospect;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasImports;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing prospects.
 *
 * @method Prospect restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ProspectService extends AbstractService
{
    use HasRestore;
    use HasImports;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'prospects';
    protected const ?string RESOURCE_CLASS = Prospect::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_prospect_status',
        'linked_organization_user',
        'assigned_organization_user',
        'assigned_organization_project',
        'source_referrer_organization_user',
        'created_by_user',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_prospect_status_id',
        'assigned_organization_user_id',
        'assigned_organization_project_id',
        'source_type',
        'email',
        'state_qualified_at_from',
        'state_qualified_at_to',
        'state_disqualified_at_from',
        'state_disqualified_at_to',
        'state_won_at_from',
        'state_won_at_to',
        'state_lost_at_from',
        'state_lost_at_to',
        'created_at_from',
        'created_at_to',
        'updated_at_from',
        'updated_at_to',
    ];

    /**
     * List all prospects.
     *
     * Resource-specific filters:
     * - `organization_prospect_status_id` (string) - Filter by prospect status
     * - `assigned_organization_user_id` (string) - Filter by assigned user
     * - `source_type` (string: inbound|outbound) - Lead source type
     * - `email` (string) - Filter by email address
     * - `state_qualified_at_from` / `state_qualified_at_to` (datetime) - Qualified date range
     * - `state_disqualified_at_from` / `state_disqualified_at_to` (datetime) - Disqualified date range
     * - `state_won_at_from` / `state_won_at_to` (datetime) - Won date range
     * - `state_lost_at_from` / `state_lost_at_to` (datetime) - Lost date range
     * - `created_at_from` / `created_at_to` (datetime) - Created date range
     * - `updated_at_from` / `updated_at_to` (datetime) - Updated date range
     *
     * @return Collection<Prospect>
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

    /**
     * Retrieve a prospect by ID.
     *
     * @param array{
     *     organization_id?: string,
     *     include?: string[],
     * } $params
     */
    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Prospect
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Create a new prospect.
     *
     * @param array{
     *     organization_id?: string,
     *     title?: string,
     *     first_name?: string,
     *     last_name?: string,
     *     company_name?: string,
     *     email?: string,
     *     phone_number?: string,
     *     phone_number_country_code?: string,
     *     country_code?: string,
     *     source_type?: string,
     *     source_channel?: string,
     *     source_campaign?: string,
     *     summary?: string,
     *     budget?: float,
     *     budget_currency?: string,
     *     organization_prospect_status_id?: string,
     *     assigned_organization_user_id?: string,
     *     assigned_organization_project_id?: string,
     * } $params
     */
    public function create(array $params, ?RequestOptions $opts = null): Prospect
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Update a prospect.
     *
     * @param array{
     *     organization_id?: string,
     *     title?: string,
     *     first_name?: string,
     *     last_name?: string,
     *     company_name?: string,
     *     email?: string,
     *     phone_number?: string,
     *     organization_prospect_status_id?: string,
     *     assigned_organization_user_id?: string,
     * } $params
     */
    public function update(string $id, array $params, ?RequestOptions $opts = null): Prospect
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Delete a prospect.
     */
    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Prospect
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Get the prospect board (kanban view).
     */
    public function board(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/board'), $params, $opts);
    }

    /**
     * Advance a prospect to the next status.
     */
    public function advance(string $id, array $params, ?RequestOptions $opts = null): Prospect
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/advance"), $params, $opts);
    }
}
