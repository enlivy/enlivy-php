<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_working_time_day_id
 * @property string $type
 * @property string|null $started_at
 * @property string|null $ended_at
 * @property int|null $minutes
 * @property bool $is_paid
 * @property string $created_at
 * @property string $updated_at
 */
class WorkingTimeDayBreak extends ApiResource
{
    public const ?string OBJECT_NAME = 'working_time_day_break';
}
