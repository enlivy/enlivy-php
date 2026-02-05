<?php

declare(strict_types=1);

namespace Enlivy\Service\BankAccount;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class BankAccountDataService extends AbstractService
{
    protected const string RESOURCE = 'bank-account-data';

    public function listBridges(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . '/bridge'), $params, $opts);
    }

    public function retrieveBridge(string $bridgeId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/bridge/{$bridgeId}"), $params, $opts);
    }

    public function initSession(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/init-session'), $params, $opts);
    }

    public function accountDetails(string $bridgeId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/bridge/{$bridgeId}/account-details"), $params, $opts);
    }

    public function pair(string $bridgeId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/bridge/{$bridgeId}/pair"), $params, $opts);
    }

    public function unpair(string $bridgeId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/bridge/{$bridgeId}/unpair"), $params, $opts);
    }

    public function getRequisition(string $bridgeId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/bridge/{$bridgeId}/requisition"), $params, $opts);
    }
}
