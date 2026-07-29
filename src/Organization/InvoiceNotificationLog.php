<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Invoice Notification Log in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_invoice_id
 * @property string $type
 * @property string|null $sent_by_user_id
 * @property string|null $sent_to
 * @property string|null $sent_to_organization_user_id
 * @property string|null $message
 * @property string|null $deleted_by_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class InvoiceNotificationLog extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice_notification_log';
}
