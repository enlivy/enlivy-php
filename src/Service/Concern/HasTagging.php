<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

trait HasTagging
{
    public function addTag(string $id, string $tagId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, static::RESOURCE . "/{$id}/tag/{$tagId}"), $params, $opts);
    }

    public function removeTag(string $id, string $tagId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, static::RESOURCE . "/{$id}/tag/{$tagId}"), $params, $opts);
    }
}
