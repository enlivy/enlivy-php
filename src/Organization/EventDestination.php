<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * `signing_secret` is answered only by `create()` and `rotateSecret()`, and only when no
 * `recipient_key` was sent; with one, `sealed_signing_secret` carries it sealed. Every other read
 * leaves both null. An installed App's destination (`type` `app`) shows no address and no secret.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $destination_url
 * @property string|null $signing_secret
 * @property array|null $sealed_signing_secret
 * @property string|null $signing_secret_rotated_at
 * @property bool $is_active
 * @property string $type
 * @property string|null $name
 * @property string|null $organization_app_installation_id
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
