<?php

declare(strict_types=1);

namespace Enlivy\Service\OAuth;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\OAuthAuthorization;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing OAuth authorizations.
 */
class OAuthAuthorizationService extends AbstractService
{
    use HasFilters;

    protected const ?string RESOURCE_CLASS = OAuthAuthorization::class;

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<OAuthAuthorization>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateFilters($params);
        /** @var Collection<OAuthAuthorization> */
        return $this->requestCollection('GET', '/oauth/authorizations', $params, $opts);
    }

    /**
     * Narrow an existing grant.
     *
     * Accepts `scopes` and/or `organizations`; each is replaced wholesale by what is sent, so send
     * the full list you want to keep. Tokens already issued are re-derived from the record, so a
     * removed scope or organization stops working at the next refresh.
     */
    public function update(string $id, array $params, ?RequestOptions $opts = null): OAuthAuthorization
    {
        /** @var OAuthAuthorization */
        return $this->request('PATCH', "/oauth/authorizations/{$id}", $params, $opts);
    }

    public function revoke(string $id, array $params = [], ?RequestOptions $opts = null): OAuthAuthorization
    {
        /** @var OAuthAuthorization */
        return $this->request('DELETE', "/oauth/authorizations/{$id}", $params, $opts);
    }

    public function info(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/oauth/authorize/info', $params, $opts, EnlivyObject::class);
    }

    /**
     * Approve a pending consent request.
     *
     * `organizations` is required. `scopes` is optional and narrows the grant to a subset of what
     * the client asked for; omit it to grant everything requested.
     */
    public function approve(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/oauth/authorize/approve', $params, $opts, EnlivyObject::class);
    }

    public function deny(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/oauth/authorize/deny', $params, $opts, EnlivyObject::class);
    }
}
