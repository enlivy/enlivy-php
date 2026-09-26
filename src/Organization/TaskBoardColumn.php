<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;
use Enlivy\EnlivyObject;

/**
 * @property string $object
 * @property string|null $organization_task_stage_id
 * @property int $total_count
 * @property bool $has_more
 * @property EnlivyObject|null $organization_task_stage
 * @property EnlivyObject $tasks
 */
class TaskBoardColumn extends ApiResource
{
    public const ?string OBJECT_NAME = 'task_board_column';
}
