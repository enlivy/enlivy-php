<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\InvitationCode;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing invitation codes.
 */
class InvitationCodeService extends AbstractService
{
    /**
     * @return Collection<InvitationCode>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', '/invitation-codes', $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvitationCode
    {
        /** @var InvitationCode */
        return $this->request('GET', "/invitation-codes/{$id}", $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): InvitationCode
    {
        /** @var InvitationCode */
        return $this->request('POST', '/invitation-codes', $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): InvitationCode
    {
        /** @var InvitationCode */
        return $this->request('PUT', "/invitation-codes/{$id}", $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): InvitationCode
    {
        /** @var InvitationCode */
        return $this->request('DELETE', "/invitation-codes/{$id}", $params, $opts);
    }

    public function restore(string $id, array $params = [], ?RequestOptions $opts = null): InvitationCode
    {
        /** @var InvitationCode */
        return $this->request('POST', "/invitation-codes/restore/{$id}", $params, $opts);
    }

    /**
     * @return Collection<InvitationCode>
     */
    public function referrals(array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', '/invitation-codes/referrals', $params, $opts);
    }

    /**
     * @return Collection<InvitationCode>
     */
    public function referralsForCode(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', "/invitation-codes/{$id}/referrals", $params, $opts);
    }
}
