<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a User Role in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_default
 * @property array $abilities
 * @property string $created_at
 * @property string $updated_at
 */
class UserRole extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_role';
}
