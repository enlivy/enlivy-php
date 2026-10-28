<?php

declare(strict_types=1);

namespace Enlivy\Service\OAuth;

use Enlivy\EnlivyObject;
use Enlivy\OAuthToken;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class OAuthTokenService extends AbstractService
{
    public function create(array $params, ?RequestOptions $opts = null): OAuthToken
    {
        /** @var OAuthToken */
        return $this->request('POST', '/oauth/token', $params, $opts, OAuthToken::class);
    }

    public function revoke(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/oauth/token/revoke', $params, $opts, EnlivyObject::class);
    }

    /**
     * Start a device sign-in (RFC 8628) for a public client: `client_id` and an optional `scope`.
     * Answers `device_code`, `user_code`, `verification_uri`, `verification_uri_complete`,
     * `expires_in` and `interval`. Show the person the code and the address, then poll `create()`
     * with `grant_type` `urn:ietf:params:oauth:grant-type:device_code`, `device_code` and `client_id`
     * every `interval` seconds; until they approve it answers a 400 whose body `error` is
     * `authorization_pending` (or `slow_down`, then wait longer), and `access_denied` or
     * `expired_token` end the wait.
     */
    public function deviceCode(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/oauth/device/code', $params, $opts, EnlivyObject::class);
    }

    public function me(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/oauth/me', null, $opts, EnlivyObject::class);
    }
}
