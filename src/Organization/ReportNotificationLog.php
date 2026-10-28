<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * One time a report was emailed. A retried delivery keeps the same row.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_report_id
 * @property string $type
 * @property string|null $sent_by_user_id
 * @property string|null $sent_to
 * @property array|null $sent_cc
 * @property string|null $sent_to_organization_user_id
 * @property string|null $subject
 * @property string|null $message
 * @property string|null $deleted_by_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class ReportNotificationLog extends ApiResource
{
    public const ?string OBJECT_NAME = 'report_notification_log';
}
