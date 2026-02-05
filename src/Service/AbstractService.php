<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\ApiResource;
use Enlivy\Collection;
use Enlivy\EnlivyClientInterface;
use Enlivy\EnlivyObject;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Util\RequestOptions;

abstract class AbstractService
{
    /**
     * The resource class this service works with.
     * Override in subclasses to enable automatic type casting.
     *
     * @var class-string<ApiResource>|null
     */
    protected const ?string RESOURCE_CLASS = null;

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
     * Make a request and return an EnlivyObject (or typed resource).
     *
     * @template T of EnlivyObject
     * @param class-string<T>|null $resourceClass Override the default resource class
     * @return T|EnlivyObject
     */
    protected function request(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
        ?string $resourceClass = null,
    ): EnlivyObject {
        $response = $this->client->getRequestor()->request($method, $path, $params, $opts);

        $data = $response->json['data'] ?? $response->json ?? [];

        $class = $resourceClass ?? static::RESOURCE_CLASS;

        if ($class !== null && is_subclass_of($class, ApiResource::class)) {
            $obj = new $class($data['id'] ?? null);
            $obj->refreshFrom($data);
            return $obj;
        }

        return EnlivyObject::constructFrom($data);
    }

    /**
     * Make a request and return a Collection (paginated list).
     *
     * @template T of EnlivyObject
     * @param class-string<T>|null $resourceClass The class for items in the collection
     * @return Collection<T>
     */
    protected function requestCollection(
        string $method,
        string $path,
        ?array $params = null,
        ?RequestOptions $opts = null,
        ?string $resourceClass = null,
    ): Collection {
        $class = $resourceClass ?? static::RESOURCE_CLASS;

        return $this->client->getRequestor()->requestCollection($method, $path, $params, $opts, $class);
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
