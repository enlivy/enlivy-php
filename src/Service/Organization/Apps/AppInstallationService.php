<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Apps;

use Enlivy\Organization\AppInstallation;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Installing an App the caller develops, with an OAuth token holding `apps:develop`, in an
 * organization where they manage services. The rest of an installation's life (reading it,
 * upgrading, changing permissions, suspending, removing, its connections) is administered in the
 * dashboard and answers no API credential.
 */
class AppInstallationService extends AbstractService
{
    use HasIncludes;

    protected const string RESOURCE = 'app-installations';
    protected const ?string RESOURCE_CLASS = AppInstallation::class;

    public const array AVAILABLE_INCLUDES = [];

    /**
     * Install the caller's own App at one of its revisions: `user_app_id`, `user_app_revision_id`, the
     * approved `permissions` (`oauth_scopes`, `connector_capabilities`, `context_fields`, each a list)
     * and who holds its capabilities (`capability_holders`: `admins` unless given, `everyone`, or
     * `roles` with `capability_holder_role_ids`). The grant is capped by the token's scopes and the
     * person's role there; a personal App taking everything sends no `permissions`.
     */
    public function create(array $params, ?RequestOptions $opts = null): AppInstallation
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var AppInstallation */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }
}
