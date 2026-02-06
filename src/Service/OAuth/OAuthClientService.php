<?php

declare(strict_types=1);

namespace Enlivy\Service\OAuth;

use Enlivy\Collection;
use Enlivy\OAuthClient;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing OAuth clients.
 */
class OAuthClientService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    public const array AVAILABLE_INCLUDES = [
        'user',
    ];

    public const array AVAILABLE_FILTERS = [
        'user_id',
        'is_active',
        'is_verified',
    ];

    /**
     * @return Collection<OAuthClient>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);

        return $this->requestCollection('GET', '/oauth/clients', $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): OAuthClient
    {
        $this->validateIncludes($params);

        /** @var OAuthClient */
        return $this->request('GET', "/oauth/clients/{$id}", $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): OAuthClient
    {
        $this->validateIncludes($params);

        /** @var OAuthClient */
        return $this->request('POST', '/oauth/clients', $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): OAuthClient
    {
        $this->validateIncludes($params);

        /** @var OAuthClient */
        return $this->request('PUT', "/oauth/clients/{$id}", $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): OAuthClient
    {
        $this->validateIncludes($params);

        /** @var OAuthClient */
        return $this->request('DELETE', "/oauth/clients/{$id}", $params, $opts);
    }

    public function restore(string $id, array $params = [], ?RequestOptions $opts = null): OAuthClient
    {
        $this->validateIncludes($params);

        /** @var OAuthClient */
        return $this->request('POST', "/oauth/clients/restore/{$id}", $params, $opts);
    }
}
