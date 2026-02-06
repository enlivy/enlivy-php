<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\BillingSchedule;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasImports;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing billing schedules.
 *
 * @method BillingSchedule restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class BillingScheduleService extends AbstractService
{
    use HasRestore;
    use HasImports;
    use HasIncludes;

    protected const string RESOURCE = 'billing-schedules';
    protected const ?string RESOURCE_CLASS = BillingSchedule::class;

    public const array AVAILABLE_INCLUDES = [
        'sender_user',
        'receiver_user',
        'contract',
        'deleted_by_user',
        'payments',
    ];

    /**
     * @return Collection<BillingSchedule>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): BillingSchedule
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
