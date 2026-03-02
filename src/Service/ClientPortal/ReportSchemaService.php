<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\ReportSchema;
use Enlivy\Util\RequestOptions;

class ReportSchemaService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = ReportSchema::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'report-schemas'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ReportSchema
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "report-schemas/{$id}"), $params, $opts);
    }
}
