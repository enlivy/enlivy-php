<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\ServiceIntegration;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class ServiceIntegrationStripeService extends AbstractService
{
    public function connect(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, 'service-integration/stripe/connect'), $params, $opts);
    }

    /**
     * Currencies detected on the connected Stripe account.
     */
    public function detectedCurrencies(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'service-integration/stripe/detected-currencies'), $params, $opts);
    }

    /**
     * Create an external bank account on the connected Stripe account.
     */
    public function createBankAccount(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, 'service-integration/stripe/bank-accounts'), $params, $opts);
    }
}
