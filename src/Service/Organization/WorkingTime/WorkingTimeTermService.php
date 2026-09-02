<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\WorkingTime;

use Enlivy\Collection;
use Enlivy\Organization\WorkingTimeTerm;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * @method WorkingTimeTerm restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class WorkingTimeTermService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'working-time-terms';
    protected const ?string RESOURCE_CLASS = WorkingTimeTerm::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_employment',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_employment_id',
        'effective_on',
    ];

    /**
     * @return Collection<WorkingTimeTerm>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<WorkingTimeTerm> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): WorkingTimeTerm
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var WorkingTimeTerm */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): WorkingTimeTerm
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var WorkingTimeTerm */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): WorkingTimeTerm
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var WorkingTimeTerm */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): WorkingTimeTerm
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var WorkingTimeTerm */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
