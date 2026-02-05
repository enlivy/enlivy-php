<?php

declare(strict_types=1);

namespace Enlivy\Service\ServiceIntegration;

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
}
