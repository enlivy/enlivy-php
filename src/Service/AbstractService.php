<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyClientInterface;
use Enlivy\EnlivyObject;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Util\RequestOptions;

abstract class AbstractService
{
    public function __construct(
        protected readonly EnlivyClientInterface $client,
    ) {}

    /**
     * Resolve the organization ID from params, opts, or client default.
     * Removes organization_id from $params if present (it's a routing concern, not a query param).
     */
    protected function resolveOrganizationId(?array &$params = null, ?RequestOptions $opts = null): string
    {
        $orgId = null;

        if (isset($params['organization_id'])) {
            $orgId = $params['organization_id'];
            unset($params['organization_id']);
        }

        $orgId ??= $opts?->organizationId;
        $orgId ??= $this->client->getOrganizationId();

        if ($orgId === null) {
            throw new InvalidArgumentException(
                'organization_id is required. Set it on the client or pass it per-request.',
            );
        }

        return $orgId;
    }

    /**
     * Build an organization-scoped path.
     */
    protected function orgPath(string $orgId, string $resource): string
    {
        return "/organizations/{$orgId}/{$resource}";
    }

    /**
     * Make a request and return an EnlivyObject.
     */
    protected function request(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
    ): EnlivyObject {
        $response = $this->client->getRequestor()->request($method, $path, $params, $opts);

        $data = $response->json['data'] ?? $response->json ?? [];

        return EnlivyObject::constructFrom($data);
    }

    /**
     * Make a request and return a Collection (paginated list).
     */
    protected function requestCollection(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
    ): Collection {
        return $this->client->getRequestor()->requestCollection($method, $path, $params, $opts);
    }

    /**
     * Make a request and return raw binary content.
     */
    protected function requestRaw(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
    ): string {
        return $this->client->getRequestor()->requestRaw($method, $path, $params, $opts);
    }
}
