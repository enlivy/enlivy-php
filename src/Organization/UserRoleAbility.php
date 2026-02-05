<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a User Role Ability in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_user_role_id
 * @property string $ability
 * @property string $created_at
 * @property string $updated_at
 */
class UserRoleAbility extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_role_ability';
}
