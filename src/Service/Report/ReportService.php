<?php

declare(strict_types=1);

namespace Enlivy\Service\Report;

use Enlivy\Collection;
use Enlivy\Report;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing reports.
 *
 * @method Report restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReportService extends AbstractService
{
    use HasRestore;

    protected const string RESOURCE = 'reports';

    /**
     * @return Collection<Report>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Report
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Report
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Report
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Report */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
