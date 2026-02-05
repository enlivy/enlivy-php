<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class UserOrganizationSettingService extends AbstractService
{
    public function retrieve(string $userId, string $orgId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/users/{$userId}/organizations/{$orgId}/settings", $params, $opts);
    }

    public function update(string $userId, string $orgId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$userId}/organizations/{$orgId}/settings", $params, $opts);
    }

    public function delete(string $userId, string $orgId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('DELETE', "/users/{$userId}/organizations/{$orgId}/settings", $params, $opts);
    }
}
