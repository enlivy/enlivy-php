<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Task Status in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $color
 * @property int $order
 * @property bool $is_default
 * @property bool $is_completed
 * @property string $created_at
 * @property string $updated_at
 */
class TaskStatus extends ApiResource
{
    public const ?string OBJECT_NAME = 'task_status';
}
