<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Resource Bundle in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property array $resources
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class ResourceBundle extends ApiResource
{
    public const ?string OBJECT_NAME = 'resource_bundle';
}
