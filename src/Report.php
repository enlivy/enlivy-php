<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Report in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_report_schema_id
 * @property string $name
 * @property array $data
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class Report extends ApiResource
{
    public const ?string OBJECT_NAME = 'report';
}
