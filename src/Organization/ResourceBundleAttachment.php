<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $organization_resource_bundle_id
 * @property string|null $organization_project_id
 * @property string $created_at
 * @property string $updated_at
 */
class ResourceBundleAttachment extends ApiResource
{
    public const ?string OBJECT_NAME = 'resource_bundle_attachment';
}
