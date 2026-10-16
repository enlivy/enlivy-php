<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $grantee_type
 * @property string|null $grantee_id
 * @property string|null $organization_project_id
 * @property string|null $organization_resource_bundle_id
 * @property string $resource_type
 * @property string|null $resource_id
 * @property list<string> $abilities
 * @property array|null $settings
 * @property string $created_at
 * @property string $updated_at
 */
class AccessGrant extends ApiResource
{
    public const ?string OBJECT_NAME = 'access_grant';
}
