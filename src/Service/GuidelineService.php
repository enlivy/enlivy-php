<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Guideline;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing guidelines.
 *
 * @method Guideline restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class GuidelineService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasDownload;

    protected const string RESOURCE = 'guidelines';

    /**
     * @return Collection<Guideline>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Guideline
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Guideline */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Guideline
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Guideline */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Guideline
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Guideline */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Guideline
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Guideline */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * @return Collection<EnlivyObject>
     */
    public function listRevisions(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/revisions"), $params, $opts);
    }

    public function retrieveRevision(string $id, string $revisionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/revisions/{$revisionId}"), $params, $opts);
    }

    public function deleteRevision(string $id, string $revisionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}/revisions/{$revisionId}"), $params, $opts);
    }
}
