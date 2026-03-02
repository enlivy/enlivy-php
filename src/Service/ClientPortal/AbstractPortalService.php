<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Service\AbstractService;

abstract class AbstractPortalService extends AbstractService
{
    /**
     * Build a client-portal-scoped path.
     */
    protected function portalPath(string $orgId, string $resource): string
    {
        return "/user-client-portal/organizations/{$orgId}/{$resource}";
    }
}
