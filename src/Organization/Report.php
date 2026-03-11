<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Report in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_report_schema_id
 * @property string|null $organization_user_id
 * @property string|null $organization_user_role_id
 * @property string|null $organization_project_id
 * @property array|null $report_map
 * @property string|null $report_date
 * @property string|null $locale
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Report extends ApiResource
{
    public const ?string OBJECT_NAME = 'report';
}
