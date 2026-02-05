<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Notification in the Enlivy API.
 *
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property string $notifiable_id
 * @property array $data
 * @property string|null $read_at
 * @property string $created_at
 * @property string $updated_at
 */
class Notification extends ApiResource
{
    public const ?string OBJECT_NAME = 'notification';
}
