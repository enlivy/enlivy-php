<?php

declare(strict_types=1);

namespace Enlivy\Service\BankAccount;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\BankAccount;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing bank accounts.
 *
 * @method BankAccount restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class BankAccountService extends AbstractService
{
    use HasRestore;
    use HasTagging;

    protected const string RESOURCE = 'bank-accounts';

    /**
     * @return Collection<BankAccount>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BankAccount
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BankAccount */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): BankAccount
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BankAccount */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): BankAccount
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BankAccount */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): BankAccount
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BankAccount */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function balance(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/balance'), $params, $opts);
    }
}
