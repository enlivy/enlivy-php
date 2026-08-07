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
 * @property string $status
 * @property array|null $request_payload
 * @property int $response_status
 * @property string|null $response_type
 * @property string|null $response
 * @property bool $is_retry
 * @property string $created_at
 * @property string $updated_at
 */
class EventDelivery extends ApiResource
{
    public const ?string OBJECT_NAME = 'event_delivery';
}
