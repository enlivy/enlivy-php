<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\ReusableContent;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing reusable content.
 */
class ReusableContentService extends AbstractService
{
    use HasIncludes;
    protected const string RESOURCE = 'reusable-content';
    protected const ?string RESOURCE_CLASS = ReusableContent::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
    ];

    /**
     * @return Collection<ReusableContent>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ReusableContent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ReusableContent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): ReusableContent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ReusableContent
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
