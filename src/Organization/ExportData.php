<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Export Data in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $status
 * @property string $type
 * @property array|null $parameters
 * @property array|null $data_schema
 * @property string|null $current_export_table
 * @property string|null $current_export_table_last_error
 * @property string|null $current_export_last_created_at
 * @property array|null $export_remaining_items
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
