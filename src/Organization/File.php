<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string|null $uploaded_by_user_id
 * @property string $organization_id
 * @property string|null $parent_organization_files_id
 * @property string $name
 * @property string|null $description
 * @property string|null $extension
 * @property int|null $size
 * @property string|null $context
 * @property bool $is_public
 * @property string|null $public_access_expires_at
 * @property string|null $file_url
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class File extends ApiResource
{
    public const ?string OBJECT_NAME = 'file';
}
