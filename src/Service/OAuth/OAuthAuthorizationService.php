<?php

declare(strict_types=1);

namespace Enlivy\Service\OAuth;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\OAuthAuthorization;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing OAuth authorizations.
 */
class OAuthAuthorizationService extends AbstractService
{
    /**
     * @return Collection<OAuthAuthorization>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', '/oauth/authorizations', $params, $opts);
    }

    public function revoke(string $id, array $params = [], ?RequestOptions $opts = null): OAuthAuthorization
    {
        /** @var OAuthAuthorization */
        return $this->request('DELETE', "/oauth/authorizations/{$id}", $params, $opts);
    }

    public function info(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/oauth/authorize/info', $params, $opts);
    }

    public function approve(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/oauth/authorize/approve', $params, $opts);
    }

    public function deny(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/oauth/authorize/deny', $params, $opts);
    }
}
