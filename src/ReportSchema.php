<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Report Schema in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property array $fields
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class ReportSchema extends ApiResource
{
    public const ?string OBJECT_NAME = 'report_schema';
}
