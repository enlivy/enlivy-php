<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

trait HasRestore
{
    public function restore(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, static::RESOURCE . "/restore/{$id}"), $params, $opts);
    }
}
