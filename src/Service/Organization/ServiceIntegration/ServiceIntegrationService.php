<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\ServiceIntegration;

use Enlivy\Collection;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class ServiceIntegrationService extends AbstractService
{
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', '/service-integration', $params, $opts);
    }
}
