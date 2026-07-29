<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Organization Notification in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $event
 * @property string|null $type
 * @property string|null $title
 * @property string|null $sent_to_email_address
 * @property string|null $sent_to_organization_user_id
 * @property string|null $sent_from_email_address
 * @property string|null $sent_from_email_name
 * @property string $created_at
 * @property string $updated_at
 */
class Notification extends ApiResource
{
    public const ?string OBJECT_NAME = 'notification';
}
