<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Webhook Endpoint in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $url
 * @property string|null $description
 * @property string $signing_secret
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Webhook extends ApiResource
{
    public const ?string OBJECT_NAME = 'webhook';
}
