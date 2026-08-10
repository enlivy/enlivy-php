<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\BlockedIdentifier;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class BlockedIdentifierService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'blocked-identifiers';
    protected const ?string RESOURCE_CLASS = BlockedIdentifier::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
    ];

    public const array AVAILABLE_FILTERS = [
        'type',
        'source',
    ];

    /**
     * `source` defaults to the organization's own rows. Pass
     * {@see \Enlivy\Enums\BlockedIdentifier\Sources::ALL} to see the
     * platform-wide entries alongside them.
     *
     * @return Collection<BlockedIdentifier>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<BlockedIdentifier> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BlockedIdentifier
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BlockedIdentifier */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * `value` is validated against the shape `type` implies, and must be unique
     * within the organization. `normalized_value` is derived server-side.
     */
    public function create(array $params, ?RequestOptions $opts = null): BlockedIdentifier
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BlockedIdentifier */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Sending `type` requires sending `value` with it — the shape rule is
     * applied to the pair, not to whichever half arrived.
     */
    public function update(string $id, array $params, ?RequestOptions $opts = null): BlockedIdentifier
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BlockedIdentifier */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): BlockedIdentifier
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BlockedIdentifier */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
