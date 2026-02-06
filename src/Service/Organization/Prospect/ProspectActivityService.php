<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Prospect;

use Enlivy\Collection;
use Enlivy\Organization\ProspectActivity;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing prospect activities.
 *
 * @method ProspectActivity restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ProspectActivityService extends AbstractService
{
    use HasRestore;
    use HasIncludes;

    protected const string RESOURCE = 'prospect-activities';
    protected const ?string RESOURCE_CLASS = ProspectActivity::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_prospect',
        'performed_by_organization_user',
        'organization_report',
        'organization_prospect_status_path',
        'created_by_user',
        'deleted_by_user',
    ];

    /**
     * @return Collection<ProspectActivity>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ProspectActivity
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ProspectActivity
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): ProspectActivity
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ProspectActivity
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
