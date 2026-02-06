<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\UserAddress;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing user addresses.
 */
class UserAddressService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_user',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<UserAddress>
     */
    public function list(string $userId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "users/{$userId}/addresses"), $params, $opts);
    }

    public function create(string $userId, array $params, ?RequestOptions $opts = null): UserAddress
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserAddress */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/addresses"), $params, $opts);
    }

    public function retrieve(string $userId, string $addressId, array $params = [], ?RequestOptions $opts = null): UserAddress
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserAddress */
        return $this->request('GET', $this->orgPath($orgId, "users/{$userId}/addresses/{$addressId}"), $params, $opts);
    }

    public function update(string $userId, string $addressId, array $params, ?RequestOptions $opts = null): UserAddress
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserAddress */
        return $this->request('PUT', $this->orgPath($orgId, "users/{$userId}/addresses/{$addressId}"), $params, $opts);
    }

    public function delete(string $userId, string $addressId, array $params = [], ?RequestOptions $opts = null): UserAddress
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserAddress */
        return $this->request('DELETE', $this->orgPath($orgId, "users/{$userId}/addresses/{$addressId}"), $params, $opts);
    }

    public function restore(string $userId, string $addressId, array $params = [], ?RequestOptions $opts = null): UserAddress
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserAddress */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/addresses/restore/{$addressId}"), $params, $opts);
    }
}
