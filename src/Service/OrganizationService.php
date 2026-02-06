<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing organizations.
 */
class OrganizationService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'remaining_credits',
        'credits',
        'membership_features',
    ];

    public const array AVAILABLE_FILTERS = [
        'created_at_from',
        'created_at_to',
        'updated_at_from',
        'updated_at_to',
    ];

    /**
     * @return Collection<Organization>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);

        return $this->requestCollection('GET', '/organizations', $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Organization
    {
        $this->validateIncludes($params);

        /** @var Organization */
        return $this->request('GET', "/organizations/{$id}", $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Organization
    {
        $this->validateIncludes($params);

        /** @var Organization */
        return $this->request('POST', '/organizations', $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Organization
    {
        $this->validateIncludes($params);

        /** @var Organization */
        return $this->request('PUT', "/organizations/{$id}", $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Organization
    {
        $this->validateIncludes($params);

        /** @var Organization */
        return $this->request('DELETE', "/organizations/{$id}", $params, $opts);
    }

    public function restore(string $id, array $params = [], ?RequestOptions $opts = null): Organization
    {
        $this->validateIncludes($params);

        /** @var Organization */
        return $this->request('POST', "/organizations/restore/{$id}", $params, $opts);
    }

    public function summary(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);

        return $this->request('GET', "/organizations/{$id}/summary", $params, $opts);
    }
}
