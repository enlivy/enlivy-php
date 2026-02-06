<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\ResourceBundle;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ResourceBundlePermissionPlaybookService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_resource_bundle',
        'organization_playbook',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_resource_bundle_id',
        'organization_playbook_id',
        'project_member_role',
    ];

    public function list(string $bundleId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-playbooks"), $params, $opts);
    }

    public function create(string $bundleId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-playbooks"), $params, $opts);
    }

    public function retrieve(string $bundleId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-playbooks/{$permissionId}"), $params, $opts);
    }

    public function update(string $bundleId, string $permissionId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-playbooks/{$permissionId}"), $params, $opts);
    }

    public function delete(string $bundleId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-playbooks/{$permissionId}"), $params, $opts);
    }
}
