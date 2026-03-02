<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Proposal;
use Enlivy\Util\RequestOptions;

class ProposalService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Proposal::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'proposals'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "proposals/{$id}"), $params, $opts);
    }

    public function accept(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/accept"), $params, $opts);
    }

    public function reject(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/reject"), $params, $opts);
    }

    public function selectPaymentMethod(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/select-payment-method"), $params, $opts);
    }

    public function paymentInstructions(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "proposals/{$id}/payment-instructions"), $params, $opts);
    }

    public function createPaymentIntent(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/create-payment-intent"), $params, $opts);
    }

    public function confirmPayment(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/confirm-payment"), $params, $opts);
    }

    public function invoicePreview(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "proposals/{$id}/invoice-preview"), $params, $opts);
    }
}
