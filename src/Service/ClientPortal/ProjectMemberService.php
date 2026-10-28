<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Util\RequestOptions;

/**
 * Who else works a project, so a member can see who to hand a prospect to.
 */
class ProjectMemberService extends AbstractPortalService
{
    public function list(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, "projects/{$projectId}/members"), $params, $opts);
    }

    /**
     * The project members a report filed in the project can be written for, open to whoever may file
     * a report there.
     */
    public function reportRecipients(string $projectId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, "reports/recipients/{$projectId}"), $params, $opts);
    }
}
