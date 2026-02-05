<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Invoice Notification Log in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_invoice_id
 * @property string $type
 * @property string $recipient
 * @property string $status
 * @property string|null $sent_at
 * @property string|null $error
 * @property string $created_at
 * @property string $updated_at
 */
class InvoiceNotificationLog extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice_notification_log';
}
