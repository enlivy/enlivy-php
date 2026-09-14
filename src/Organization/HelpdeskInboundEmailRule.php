<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_inbox_id
 * @property string $name
 * @property string|null $from_email_address
 * @property string|null $from_domain
 * @property string|null $subject_contains
 * @property string|null $to_email_address
 * @property string $action
 * @property int $order
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class HelpdeskInboundEmailRule extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_inbound_email_rule';
}
