<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Apps;

use Enlivy\Organization\AppInstallRequest;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Asking an organization the caller belongs to, with an OAuth token holding `apps:develop`, to install
 * their personal App. One request waits per App and organization; an admin answers it in the
 * dashboard, and `userApps->installRequests()` shows what each organization answered. Reading,
 * cancelling, approving and declining requests is the dashboard's and answers no API credential.
 */
class AppInstallRequestService extends AbstractService
{
    use HasIncludes;

    protected const string RESOURCE = 'app-install-requests';
    protected const ?string RESOURCE_CLASS = AppInstallRequest::class;

    public const array AVAILABLE_INCLUDES = [];

    /**
     * Ask the organization to install `user_app_id`, an App the caller develops.
     */
    public function create(array $params, ?RequestOptions $opts = null): AppInstallRequest
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var AppInstallRequest */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }
}
