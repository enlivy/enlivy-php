<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a File in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $parent_organization_files_id
 * @property string|null $uploaded_by_user_id
 * @property string $name
 * @property string|null $description
 * @property string|null $extension
 * @property string|null $mime_type
 * @property int|null $size
 * @property string|null $context
 * @property string|null $disk_alias
 * @property string|null $disk_path
 * @property string|null $file_url
 * @property bool $is_public
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class File extends ApiResource
{
    public const ?string OBJECT_NAME = 'file';
}
