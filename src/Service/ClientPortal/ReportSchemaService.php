<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\ReportSchema;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ReportSchemaService extends AbstractPortalService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = ReportSchema::class;

    public const array AVAILABLE_INCLUDES = [
        'report_schema_fields',
    ];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'report-schemas'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ReportSchema
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ReportSchema */
        return $this->request('GET', $this->portalPath($orgId, "report-schemas/{$id}"), $params, $opts);
    }
}
