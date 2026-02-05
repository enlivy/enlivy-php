<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Export Data in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $created_by_user_id
 * @property string $status
 * @property array|null $data_schema
 * @property array|null $export_remaining_items
 * @property string|null $disk_alias
 * @property string|null $disk_path
 * @property string|null $job_pickup_at
 * @property string|null $job_started_at
 * @property string|null $completed_at
 * @property string|null $available_until
 * @property string $created_at
 * @property string $updated_at
 */
class ExportData extends ApiResource
{
    public const ?string OBJECT_NAME = 'export_data';
}
