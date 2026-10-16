<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\Project;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ProjectService extends AbstractPortalService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = Project::class;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
        'tag_ids',
        'access',
        'available_pipelines',
    ];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'projects'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Project
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Project */
        return $this->request('GET', $this->portalPath($orgId, "projects/{$id}"), $params, $opts);
    }
}
