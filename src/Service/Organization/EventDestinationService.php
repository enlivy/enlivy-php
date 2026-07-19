<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\EventDelivery;
use Enlivy\Organization\EventDestination;
use Enlivy\Organization\EventSubscription;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class EventDestinationService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'event-destinations';
    protected const ?string RESOURCE_CLASS = EventDestination::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'deleted_by_user',
        'event_subscriptions',
        'event_deliveries',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<EventDestination>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<EventDestination> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): EventDestination
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var EventDestination */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): EventDestination
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var EventDestination */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): EventDestination
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var EventDestination */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): EventDestination
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var EventDestination */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * List event subscriptions across the organization's destinations.
     *
     * @return Collection<EventSubscription>
     */
    public function subscriptions(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . '/subscriptions'), $params, $opts, EventSubscription::class);
    }

    /**
     * List delivery attempts across the organization's destinations.
     *
     * Filters: `organization_event_destination_id`, `event`, `status`,
     * `created_at_from`/`created_at_to`, `updated_at_from`/`updated_at_to`.
     *
     * @return Collection<EventDelivery>
     */
    public function deliveries(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . '/deliveries'), $params, $opts, EventDelivery::class);
    }

    public function retrieveDelivery(string $id, array $params = [], ?RequestOptions $opts = null): EventDelivery
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var EventDelivery */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/deliveries/{$id}"), $params, $opts, EventDelivery::class);
    }
}
