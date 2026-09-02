<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_employment_id
 * @property string $date
 * @property string $disposition
 * @property string $origin
 * @property string|null $recorded_by_user_id
 * @property string|null $recorded_at
 * @property string|null $attested_by_user_id
 * @property string|null $attested_by_organization_user_id
 * @property string|null $attested_at
 * @property string|null $attestation_method
 * @property string $attestation_status
 * @property int|null $last_attested_version
 * @property string|null $source_type
 * @property string|null $source_reference
 * @property string|null $started_at
 * @property string|null $ended_at
 * @property int|null $break_minutes
 * @property float|null $worked_hours
 * @property float|null $worked_days
 * @property float|null $night_hours
 * @property float|null $overtime_hours
 * @property float|null $weekend_hours
 * @property string|null $absence_reason
 * @property bool $absence_reason_redacted
 * @property string|null $work_site
 * @property int $version
 * @property string|null $content_hash
 * @property string|null $derived_at
 * @property string $created_at
 * @property string $updated_at
 */
class WorkingTimeDay extends ApiResource
{
    public const ?string OBJECT_NAME = 'working_time_day';
}
