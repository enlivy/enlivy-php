<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\UserPaymentMethod;
use Enlivy\Util\RequestOptions;

/**
 * Client Portal service for a customer to manage their own payment methods.
 */
class PaymentMethodService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = UserPaymentMethod::class;

    /**
     * @return Collection<UserPaymentMethod>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<UserPaymentMethod> */
        return $this->requestCollection('GET', $this->portalPath($orgId, 'payment-methods'), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): UserPaymentMethod
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('POST', $this->portalPath($orgId, 'payment-methods'), $params, $opts);
    }

    /**
     * Create a Stripe SetupIntent for adding a new card from the portal.
     */
    public function setupIntent(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, 'payment-methods/setup-intent'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('GET', $this->portalPath($orgId, "payment-methods/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('DELETE', $this->portalPath($orgId, "payment-methods/{$id}"), $params, $opts);
    }

    public function setAsDefault(string $id, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('POST', $this->portalPath($orgId, "payment-methods/{$id}/set-as-default"), $params, $opts);
    }
}
