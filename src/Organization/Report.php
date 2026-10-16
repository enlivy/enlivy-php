<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string $organization_report_schema_id
 * @property string|null $organization_user_id
 * @property string|null $organization_user_role_id
 * @property string|null $organization_task_id
 * @property string|null $recipient_organization_user_id
 * @property string $status
 * @property string $acknowledgement
 * @property bool $awaiting_acknowledgement
 * @property array|null $report_map
 * @property string|null $locale
 * @property string|null $report_date
 * @property string|null $period_from
 * @property string|null $period_to
 * @property int|null $worked_minutes
 * @property string|null $submitted_at
 * @property string|null $acknowledged_at
 * @property string|null $acknowledged_by_organization_user_id
 * @property bool|null $can_edit
 * @property string|null $can_edit_until
 * @property bool|null $can_acknowledge
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Report extends ApiResource
{
    public const ?string OBJECT_NAME = 'report';
}
