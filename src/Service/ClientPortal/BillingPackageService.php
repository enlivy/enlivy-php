<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\BillingPackage;
use Enlivy\Organization\Proposal;
use Enlivy\Util\RequestOptions;

class BillingPackageService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = BillingPackage::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'billing-packages'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BillingPackage
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BillingPackage */
        return $this->request('GET', $this->portalPath($orgId, "billing-packages/{$id}"), $params, $opts);
    }

    /**
     * @param array{
     *     organization_id?: string,
     *     organization_billing_package_subscription_term_id?: string,
     *     organization_billing_package_payment_plan_id?: string,
     *     selected_group_items?: list<array{id: string, quantity?: int}>,
     *     line_quantities?: list<array{id: string, quantity: int}>,
     *     include?: string|list<string>,
     * } $params
     */
    public function claim(string $id, array $params = [], ?RequestOptions $opts = null): Proposal
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Proposal */
        return $this->request('POST', $this->portalPath($orgId, "billing-packages/{$id}/claim"), $params, $opts, Proposal::class);
    }

    /**
     * Open a checkout session bought by the signed-in customer, for packages that allow it. The browser's token
     * is on the response meta as `client_token`, shown once: hand it to the browser that runs the checkout, which
     * sends it as `X-Enlivy-Checkout-Session-Token`.
     *
     * @param array{
     *     organization_id?: string,
     *     organization_billing_package_subscription_term_id?: string,
     *     organization_billing_package_payment_plan_id?: string,
     *     selected_group_items?: list<array{id: string, quantity?: int}>,
     *     line_quantities?: list<array{id: string, quantity: int}>,
     *     currency?: string,
     *     source_channel?: string,
     *     source_medium?: string,
     *     source_campaign?: string,
     *     source_term?: string,
     *     source_content?: string,
     *     source_click_id?: string,
     * } $params
     */
    public function openCheckoutSession(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->portalPath($orgId, "billing-packages/{$id}/checkout-session"), $params, $opts, EnlivyObject::class);
    }
}
