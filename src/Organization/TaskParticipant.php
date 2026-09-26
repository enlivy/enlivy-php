<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_task_id
 * @property string $organization_user_id
 * @property string $role
 * @property string $source
 * @property bool $notifications_enabled
 * @property string|null $assigned_by_organization_user_id
 * @property string|null $assigned_at
 * @property string $created_at
 * @property string $updated_at
 */
class TaskParticipant extends ApiResource
{
    public const ?string OBJECT_NAME = 'task_participant';
}
