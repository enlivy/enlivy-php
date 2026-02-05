<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\ExportData;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing export data.
 */
class ExportDataService extends AbstractService
{
    use HasDownload;

    protected const string RESOURCE = 'export-data';

    /**
     * @return Collection<ExportData>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ExportData
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ExportData */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ExportData
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ExportData */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ExportData
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ExportData */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function retry(string $id, array $params = [], ?RequestOptions $opts = null): ExportData
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ExportData */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/retry"), $params, $opts);
    }
}
