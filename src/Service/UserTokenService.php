<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\UserToken;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing user API tokens.
 */
class UserTokenService extends AbstractService
{
    /**
     * @return Collection<UserToken>
     */
    public function list(string $userId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', "/users/{$userId}/api-tokens", $params, $opts);
    }

    public function create(string $userId, array $params, ?RequestOptions $opts = null): UserToken
    {
        /** @var UserToken */
        return $this->request('POST', "/users/{$userId}/api-tokens", $params, $opts);
    }

    public function retrieve(string $userId, string $tokenId, array $params = [], ?RequestOptions $opts = null): UserToken
    {
        /** @var UserToken */
        return $this->request('GET', "/users/{$userId}/api-tokens/{$tokenId}", $params, $opts);
    }

    public function update(string $userId, string $tokenId, array $params, ?RequestOptions $opts = null): UserToken
    {
        /** @var UserToken */
        return $this->request('PUT', "/users/{$userId}/api-tokens/{$tokenId}", $params, $opts);
    }

    public function delete(string $userId, string $tokenId, array $params = [], ?RequestOptions $opts = null): UserToken
    {
        /** @var UserToken */
        return $this->request('DELETE', "/users/{$userId}/api-tokens/{$tokenId}", $params, $opts);
    }
}
