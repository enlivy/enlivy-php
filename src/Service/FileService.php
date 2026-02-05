<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\File;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing files.
 *
 * @method File restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class FileService extends AbstractService
{
    use HasRestore;
    use HasTagging;

    protected const string RESOURCE = 'files';

    /**
     * @return Collection<File>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): File
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var File */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): File
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var File */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): File
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var File */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): File
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var File */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
