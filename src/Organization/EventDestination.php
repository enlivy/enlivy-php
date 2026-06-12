<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Event Destination in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $destination_url
 * @property string|null $signing_secret
 * @property bool $is_active
 * @property string $type
 * @property string|null $name
 * @property string|null $organization_api_credential_id
 * @property array|null $config
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class EventDestination extends ApiResource
{
    public const ?string OBJECT_NAME = 'event_destination';
}
