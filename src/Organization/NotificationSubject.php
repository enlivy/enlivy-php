<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_notification_id
 * @property string $subject_type
 * @property string $subject_id
 * @property string|null $created_at
 */
class NotificationSubject extends ApiResource
{
    public const ?string OBJECT_NAME = 'notification_subject';
}
