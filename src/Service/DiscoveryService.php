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

    /**
     * The App builder's index: where to start, each section with its path and size, and the schemas.
     * Public, no credentials needed.
     */
    public function apps(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/discovery/apps', null, $opts, EnlivyObject::class);
    }

    /**
     * One section of the App builder (`overview`, `manifest`, `fields`, `resources`, `pages_and_menu`,
     * `presentations`, `translations`, `protocol`, `connection`, `registration`, `development`,
     * `scopes`, `example`), as `apps()` lists them.
     */
    public function appsSection(string $section, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/discovery/apps/{$section}", null, $opts, EnlivyObject::class);
    }

    /**
     * One JSON schema the App builder validates against (`manifest`, `presentation`, `field`,
     * `provider_request`, `provider_response`, `provider_access`, `connection`, `sealed_secret`).
     */
    public function appsSchema(string $schema, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/discovery/apps/schemas/{$schema}", null, $opts, EnlivyObject::class);
    }
}
