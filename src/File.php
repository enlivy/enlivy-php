<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a File in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $original_name
 * @property string $path
 * @property string $disk
 * @property string $mime_type
 * @property int $size
 * @property string $extension
 * @property array $metadata
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class File extends ApiResource
{
    public const ?string OBJECT_NAME = 'file';
}
