<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\Proposal;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing proposals.
 *
 * @method Proposal restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ProposalService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'proposals';
    protected const ?string RESOURCE_CLASS = Proposal::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'project',
        'offer',
        'offer_payment_plan',
        'payments',
        'prospect',
        'receiver_user',
        'sender_user',
        'contract',
        'contract_default_sender_user',
        'created_by_user',
        'deleted_by_user',
        'expired_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'status',
        'currency',
        'organization_project_id',
        'organization_offer_id',
        'organization_prospect_id',
        'organization_receiver_user_id',
    ];

    /**
     * List all proposals.
     *
     * Resource-specific filters:
     * - `status` (string: draft|sent|viewed|accepted|rejected|expired)
     * - `currency` (string) - Filter by currency code (3 chars, e.g. EUR)
     * - `organization_project_id` (string) - Filter by project
     * - `organization_offer_id` (string) - Filter by offer
     * - `organization_prospect_id` (string) - Filter by prospect
     * - `organization_receiver_user_id` (string) - Filter by receiver user
     *
     * @return Collection<Proposal>
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

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function fromOffer(array $params, ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/from-offer'), $params, $opts);
    }

    public function send(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/send"), $params, $opts);
    }

    public function accept(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/accept"), $params, $opts);
    }

    public function reject(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/reject"), $params, $opts);
    }

    public function expire(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/expire"), $params, $opts);
    }
}
