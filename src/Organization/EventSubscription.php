<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_event_destination_id
 * @property string $event
 * @property array|null $includes
 * @property string|null $config
 */
class EventSubscription extends ApiResource
{
    public const ?string OBJECT_NAME = 'event_subscription';
}
