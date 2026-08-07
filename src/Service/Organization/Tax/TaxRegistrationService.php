<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Tax;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\TaxRegistration;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * @method TaxRegistration restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class TaxRegistrationService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tax-registrations';
    protected const ?string RESOURCE_CLASS = TaxRegistration::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
    ];

    public const array AVAILABLE_FILTERS = [
        'country_code',
        'scheme',
    ];

    /**
     * @return Collection<TaxRegistration>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<TaxRegistration> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): TaxRegistration
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxRegistration */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): TaxRegistration
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxRegistration */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): TaxRegistration
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxRegistration */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): TaxRegistration
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var TaxRegistration */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * Suggested registrations derived from the organization's profile and
     * transaction footprint. Returns raw suggestion rows (not persisted).
     */
    public function suggested(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . '/suggested'), $params, $opts, EnlivyObject::class);
    }
}
