<?php

declare(strict_types=1);

namespace Enlivy\Service\User;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class UserPortalDomainService extends AbstractService
{
    protected const string RESOURCE = 'user-portal-domain';

    public function retrieve(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function delete(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function verify(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/verify'), $params, $opts);
    }
}
