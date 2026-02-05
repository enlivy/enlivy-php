<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\Proposal;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing proposals.
 *
 * @method Proposal restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ProposalService extends AbstractService
{
    use HasRestore;

    protected const string RESOURCE = 'proposals';

    /**
     * @return Collection<Proposal>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function fromOffer(array $params, ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/from-offer'), $params, $opts);
    }

    public function send(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/send"), $params, $opts);
    }

    public function accept(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/accept"), $params, $opts);
    }

    public function reject(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/reject"), $params, $opts);
    }

    public function expire(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/expire"), $params, $opts);
    }
}
