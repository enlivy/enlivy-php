<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\ResourceBundle;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

class ResourceBundlePermissionGuidelineService extends AbstractService
{
    public function list(string $bundleId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-guidelines"), $params, $opts);
    }

    public function create(string $bundleId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-guidelines"), $params, $opts);
    }

    public function retrieve(string $bundleId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-guidelines/{$permissionId}"), $params, $opts);
    }

    public function update(string $bundleId, string $permissionId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-guidelines/{$permissionId}"), $params, $opts);
    }

    public function delete(string $bundleId, string $permissionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "resource-bundles/{$bundleId}/permission-guidelines/{$permissionId}"), $params, $opts);
    }
}
