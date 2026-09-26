<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $parent_organization_task_id
 * @property string|null $organization_task_stage_id
 * @property string $title
 * @property string|null $content
 * @property string $status
 * @property string|null $status_changed_at
 * @property string|null $board_rank
 * @property string $origin
 * @property string|null $due_at
 * @property string|null $completed_at
 * @property string|null $completed_by_organization_user_id
 * @property string|null $cancelled_at
 * @property string|null $cancelled_by_organization_user_id
 * @property string|null $cancel_reason
 * @property string|null $created_by_organization_user_id
 * @property int $children_count
 * @property int $open_children_count
 * @property int $completed_children_count
 * @property int $comments_count
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Task extends ApiResource
{
    public const ?string OBJECT_NAME = 'task';
}
