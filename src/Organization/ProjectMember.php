<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Project Member in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_project_id
 * @property string $organization_user_id
 * @property string $role
 * @property array $permissions
 * @property string $created_at
 * @property string $updated_at
 */
class ProjectMember extends ApiResource
{
    public const ?string OBJECT_NAME = 'project_member';
}
