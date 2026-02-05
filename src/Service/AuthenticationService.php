<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

class AuthenticationService extends AbstractService
{
    public function login(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/authentication/login', $params, $opts);
    }

    public function register(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/authentication/register', $params, $opts);
    }

    public function forgotPassword(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/authentication/forgot-password', $params, $opts);
    }

    public function resetPassword(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', '/authentication/reset-password', $params, $opts);
    }

    public function logout(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/authentication/logout', null, $opts);
    }

    public function listTokens(?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', '/authentication/tokens', null, $opts);
    }

    public function deleteToken(string $id, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('DELETE', "/authentication/tokens/{$id}", null, $opts);
    }
}
