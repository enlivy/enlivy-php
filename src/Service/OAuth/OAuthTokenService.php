<?php

declare(strict_types=1);

namespace Enlivy\Service\OAuth;

use Enlivy\EnlivyObject;
use Enlivy\OAuthToken;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing OAuth tokens.
 */
class OAuthTokenService extends AbstractService
{
    public function create(array $params, ?RequestOptions $opts = null): OAuthToken
    {
        /** @var OAuthToken */
        return $this->request('POST', '/oauth/token', $params, $opts);
    }

    public function revoke(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/oauth/token/revoke', $params, $opts);
    }

    public function me(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/oauth/me', null, $opts);
    }
}
