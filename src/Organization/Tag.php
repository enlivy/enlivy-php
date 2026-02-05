<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tag in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $color
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 */
class Tag extends ApiResource
{
    public const ?string OBJECT_NAME = 'tag';
}
