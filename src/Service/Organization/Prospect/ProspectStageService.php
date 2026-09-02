<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Prospect;

use Enlivy\Collection;
use Enlivy\Organization\ProspectStage;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasReorder;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * @method ProspectStage restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ProspectStageService extends AbstractService
{
    use HasRestore;
    use HasReorder;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'prospect-stages';
    protected const ?string RESOURCE_CLASS = ProspectStage::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'deleted_by_user',
        'paths',
        'pipeline',
    ];

    public const array AVAILABLE_FILTERS = [
        'title',
        'description',
        'organization_prospect_pipeline_id',
    ];

    /**
     * @return Collection<ProspectStage>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ProspectStage> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ProspectStage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ProspectStage */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ProspectStage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ProspectStage */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): ProspectStage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ProspectStage */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ProspectStage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ProspectStage */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
