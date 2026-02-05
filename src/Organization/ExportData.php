<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Export Data in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $type
 * @property string $status
 * @property string|null $file_path
 * @property array $filters
 * @property string|null $created_by_user_id
 * @property string|null $completed_at
 * @property string $created_at
 * @property string $updated_at
 */
class ExportData extends ApiResource
{
    public const ?string OBJECT_NAME = 'export_data';
}
