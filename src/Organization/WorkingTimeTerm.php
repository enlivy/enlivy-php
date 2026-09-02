<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_employment_id
 * @property string|null $effective_from
 * @property string|null $effective_to
 * @property float|null $norm_hours_per_day
 * @property float|null $norm_days_per_week
 * @property string|null $unit
 * @property array|null $schedule_pattern
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class WorkingTimeTerm extends ApiResource
{
    public const ?string OBJECT_NAME = 'working_time_term';
}
