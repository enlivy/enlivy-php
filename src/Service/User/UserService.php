<?php

declare(strict_types=1);

namespace Enlivy\Service\User;

use Enlivy\EnlivyObject;
use Enlivy\User;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing users.
 */
class UserService extends AbstractService
{
    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): User
    {
        /** @var User */
        return $this->request('GET', "/users/{$id}", $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): User
    {
        /** @var User */
        return $this->request('PUT', "/users/{$id}", $params, $opts);
    }

    public function sendEmailVerificationToken(string $id, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/users/{$id}/email/verification-token", null, $opts);
    }

    public function confirmEmailVerificationToken(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$id}/email/confirm-verification-token", $params, $opts);
    }

    public function sendPhoneVerificationToken(string $id, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/users/{$id}/phone/verification-token", null, $opts);
    }

    public function confirmPhoneVerificationToken(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$id}/phone/confirm-verification-token", $params, $opts);
    }

    public function updatePhone(string $id, array $params, ?RequestOptions $opts = null): User
    {
        /** @var User */
        return $this->request('PUT', "/users/{$id}/phone", $params, $opts);
    }

    public function requestUnlinkPhone(string $id, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$id}/phone/request-unlink", null, $opts);
    }

    public function unlinkPhone(string $id, ?RequestOptions $opts = null): User
    {
        /** @var User */
        return $this->request('DELETE', "/users/{$id}/phone", null, $opts);
    }

    public function sendSummaryEmail(string $id, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/users/{$id}/summary-email", null, $opts);
    }
}
