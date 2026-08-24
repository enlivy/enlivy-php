<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_proposal_id
 * @property string $type
 * @property bool $is_seller_notification
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
class ProposalNotificationLog extends ApiResource
{
    public const ?string OBJECT_NAME = 'proposal_notification_log';
}
