<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

/**
 * Service for the API Discovery endpoint.
 *
 * The Discovery API provides self-documenting resource metadata including
 * fields, types, endpoints, includes, and broadcast channels.
 */
class DiscoveryService extends AbstractService
{
    /**
     * Returns a lightweight index of resources with endpoint counts.
     */
    public function list(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/discovery', null, $opts);
    }

    /**
     * Returns field definitions, endpoints, includes, and broadcast info.
     *
     * @param string $name Resource name (e.g. 'organization_invoices')
     */
    public function resource(string $name, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/discovery', ['resource' => $name], $opts);
    }

    public function portalList(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/discovery/organization-user-client-portal', null, $opts);
    }

    /**
     * Get detailed metadata for a specific Client Portal resource.
     *
     * @param string $name Resource name (e.g. 'organization_invoices')
     */
    public function portalResource(string $name, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/discovery/organization-user-client-portal', ['resource' => $name], $opts);
    }
}
