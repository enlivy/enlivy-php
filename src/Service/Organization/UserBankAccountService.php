<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\UserBankAccount;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class UserBankAccountService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const ?string RESOURCE_CLASS = UserBankAccount::class;

    public const array AVAILABLE_INCLUDES = [
        'organization_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'type',
        'is_primary',
    ];

    /**
     * @return Collection<UserBankAccount>
     */
    public function list(string $userId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<UserBankAccount> */
        return $this->requestCollection('GET', $this->orgPath($orgId, "users/{$userId}/bank-accounts"), $params, $opts);
    }

    public function create(string $userId, array $params, ?RequestOptions $opts = null): UserBankAccount
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserBankAccount */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/bank-accounts"), $params, $opts);
    }

    public function retrieve(string $userId, string $bankAccountId, array $params = [], ?RequestOptions $opts = null): UserBankAccount
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserBankAccount */
        return $this->request('GET', $this->orgPath($orgId, "users/{$userId}/bank-accounts/{$bankAccountId}"), $params, $opts);
    }

    public function update(string $userId, string $bankAccountId, array $params, ?RequestOptions $opts = null): UserBankAccount
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserBankAccount */
        return $this->request('PUT', $this->orgPath($orgId, "users/{$userId}/bank-accounts/{$bankAccountId}"), $params, $opts);
    }

    public function delete(string $userId, string $bankAccountId, array $params = [], ?RequestOptions $opts = null): UserBankAccount
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserBankAccount */
        return $this->request('DELETE', $this->orgPath($orgId, "users/{$userId}/bank-accounts/{$bankAccountId}"), $params, $opts);
    }

    public function restore(string $userId, string $bankAccountId, array $params = [], ?RequestOptions $opts = null): UserBankAccount
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserBankAccount */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/bank-accounts/restore/{$bankAccountId}"), $params, $opts);
    }

    public function setPrimary(string $userId, string $bankAccountId, array $params = [], ?RequestOptions $opts = null): UserBankAccount
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserBankAccount */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/bank-accounts/{$bankAccountId}/set-primary"), $params, $opts);
    }
}
