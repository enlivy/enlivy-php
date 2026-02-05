<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a User Role in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $parent_organization_user_role_id
 * @property string $name
 * @property string|null $alias
 * @property string|null $description
 * @property bool $can_be_invoicing
 * @property bool $can_be_invoiced
 * @property bool $is_business_entity
 * @property bool $can_use_backoffice
 * @property bool $can_be_payrolled
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class UserRole extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_role';
}
