<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Prospect;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Prospect;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasImports;
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

    protected const string RESOURCE = 'prospects';

    /**
     * List all prospects.
     *
     * @param array{
     *     organization_id?: string,
     *     page?: int,
     *     per_page?: int,
     *     include?: string[],
     *     filter?: array,
     *     sort?: string,
     * } $params
     * @return Collection<Prospect>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
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
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
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
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
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
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Delete a prospect.
     */
    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Get the prospect board (kanban view).
     */
    public function board(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/board'), $params, $opts);
    }

    /**
     * Advance a prospect to the next status.
     */
    public function advance(string $id, array $params, ?RequestOptions $opts = null): Prospect
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Prospect */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/advance"), $params, $opts);
    }
}
