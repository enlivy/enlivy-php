<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

/**
 * Magic-authentication session selection.
 *
 * When a portal session resolves to more than one organization user (e.g. the
 * same email belongs to several users), the session enters an awaiting-selection
 * state: list the candidates, then bind the session to one of them.
 */
class SessionService extends AbstractPortalService
{
    /**
     * @return Collection<EnlivyObject>
     */
    public function candidateUsers(array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', $this->portalRootPath('session/candidate-users'), $params, $opts);
    }

    public function bindUser(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', $this->portalRootPath('session/bind-user'), $params, $opts);
    }
}
