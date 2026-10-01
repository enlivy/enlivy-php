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

        /** @var Proposal */
        return $this->request('GET', $this->portalPath($orgId, "proposals/{$id}"), $params, $opts);
    }

    /**
     * @param array{
     *     organization_id?: string,
     *     billed_currency?: string,
     *     displayed_amount?: float,
     * } $params
     */
    public function accept(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/accept"), $params, $opts);
    }

    /**
     * Re-quote the held conversion for a proposal that settles in a currency other than its own.
     */
    public function refreshConversion(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/refresh-conversion"), $params, $opts);
    }

    public function reject(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/reject"), $params, $opts);
    }

    /**
     * Pay the first payment of an accepted proposal. A card answers `payment_method_kind`, `payment_provider`
     * and what Stripe.js confirms; a bank transfer answers `payment_method_kind` and the transfer instructions.
     *
     * @param array{
     *     organization_id?: string,
     *     payment_method_kind: 'card'|'bank_transfer',
     *     organization_user_payment_method_id?: string|null,
     * } $params
     */
    public function pay(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/pay"), $params, $opts, EnlivyObject::class);
    }

    public function confirmPayment(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "proposals/{$id}/confirm-payment"), $params, $opts);
    }

    public function paymentInstructions(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "proposals/{$id}/payment-instructions"), $params, $opts);
    }

    public function invoicePreview(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "proposals/{$id}/invoice-preview"), $params, $opts);
    }
}
