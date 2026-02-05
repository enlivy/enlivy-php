<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Report Schema Field in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_report_schema_id
 * @property string $name
 * @property string $type
 * @property array $options
 * @property bool $is_required
 * @property int $order
 * @property string $created_at
 * @property string $updated_at
 */
class ReportSchemaField extends ApiResource
{
    public const ?string OBJECT_NAME = 'report_schema_field';
}
