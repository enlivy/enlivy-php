<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Service\AbstractService;

abstract class AbstractPortalService extends AbstractService
{
    protected function portalPath(string $orgId, string $resource): string
    {
        return $this->portalRootPath("organizations/{$orgId}/{$resource}");
    }

    /**
     * Build a client-portal path that is not scoped to an organization
     * (e.g. session-selection endpoints).
     */
    protected function portalRootPath(string $resource): string
    {
        return "/user-client-portal/{$resource}";
    }
}
