<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Proposal;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
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
        'billing_package',
        'billing_package_payment_plan',
        'subscription_term',
        'payments',
        'proposal_contracts',
        'billing_schedule',
        'invoice',
        'proforma_invoice',
        'prospect',
        'receiver_user',
        'sender_user',
        'created_by_user',
        'deleted_by_user',
        'expired_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'status',
        'currency',
        'organization_project_id',
        'organization_billing_package_id',
        'organization_prospect_id',
        'organization_receiver_user_id',
    ];

    /**
     * @return Collection<Proposal>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<Proposal> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function fromBillingPackage(array $params, ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/from-billing-package'), $params, $opts);
    }

    public function send(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/send"), $params, $opts);
    }

    public function accept(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/accept"), $params, $opts);
    }

    /**
     * Manually create a billing schedule from an accepted subscription proposal.
     */
    public function createBillingSchedule(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/billing-schedule"), $params, $opts);
    }

    public function reject(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/reject"), $params, $opts);
    }

    public function expire(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/expire"), $params, $opts);
    }

    public function email(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/email"), $params, $opts);
    }

    public function attachContract(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/contracts"), $params, $opts);
    }

    public function detachContract(string $id, string $contractId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}/contracts/{$contractId}"), $params, $opts);
    }
}
