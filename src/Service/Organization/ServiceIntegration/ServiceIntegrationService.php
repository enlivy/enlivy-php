<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\ServiceIntegration;

use Enlivy\Collection;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Util\RequestOptions;

class ServiceIntegrationService extends AbstractService
{
    use HasFilters;

    public const array AVAILABLE_FILTERS = [];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateFilters($params);
        return $this->requestCollection('GET', '/service-integration', $params, $opts);
    }
}
