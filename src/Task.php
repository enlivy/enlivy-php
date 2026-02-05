<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Task in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_task_status_id
 * @property string|null $organization_user_id
 * @property string $title
 * @property string|null $description
 * @property string $priority
 * @property string|null $due_at
 * @property string|null $completed_at
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class Task extends ApiResource
{
    public const ?string OBJECT_NAME = 'task';
}
