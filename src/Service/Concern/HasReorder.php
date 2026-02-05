<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

trait HasReorder
{
    public function reorder(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, static::RESOURCE . '/reorder'), $params, $opts);
    }
}
