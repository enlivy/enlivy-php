<?php

declare(strict_types=1);

namespace Enlivy\Service\Report;

use Enlivy\Collection;
use Enlivy\ReportSchemaField;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing report schema fields.
 */
class ReportSchemaFieldService extends AbstractService
{
    /**
     * @return Collection<ReportSchemaField>
     */
    public function list(string $schemaId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, "report-schemas/{$schemaId}/fields"), $params, $opts);
    }

    public function create(string $schemaId, array $params, ?RequestOptions $opts = null): ReportSchemaField
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ReportSchemaField */
        return $this->request('POST', $this->orgPath($orgId, "report-schemas/{$schemaId}/fields"), $params, $opts);
    }

    public function retrieve(string $schemaId, string $fieldId, array $params = [], ?RequestOptions $opts = null): ReportSchemaField
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ReportSchemaField */
        return $this->request('GET', $this->orgPath($orgId, "report-schemas/{$schemaId}/fields/{$fieldId}"), $params, $opts);
    }

    public function update(string $schemaId, string $fieldId, array $params, ?RequestOptions $opts = null): ReportSchemaField
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ReportSchemaField */
        return $this->request('PUT', $this->orgPath($orgId, "report-schemas/{$schemaId}/fields/{$fieldId}"), $params, $opts);
    }

    public function delete(string $schemaId, string $fieldId, array $params = [], ?RequestOptions $opts = null): ReportSchemaField
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ReportSchemaField */
        return $this->request('DELETE', $this->orgPath($orgId, "report-schemas/{$schemaId}/fields/{$fieldId}"), $params, $opts);
    }

    public function restore(string $schemaId, string $fieldId, array $params = [], ?RequestOptions $opts = null): ReportSchemaField
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ReportSchemaField */
        return $this->request('POST', $this->orgPath($orgId, "report-schemas/{$schemaId}/fields/restore/{$fieldId}"), $params, $opts);
    }
}
