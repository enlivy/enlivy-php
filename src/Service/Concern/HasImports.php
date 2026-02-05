<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

trait HasImports
{
    public function importCreate(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, static::RESOURCE . '/imports'), $params, $opts);
    }

    public function importList(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, static::RESOURCE . '/imports'), $params, $opts);
    }

    public function importRetrieve(string $importId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, static::RESOURCE . "/imports/{$importId}"), $params, $opts);
    }
}
