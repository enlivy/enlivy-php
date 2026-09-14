<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_conversation_id
 * @property string|null $reader_organization_user_id
 * @property string|null $reader_organization_helpdesk_visitor_id
 * @property string $last_read_organization_helpdesk_conversation_message_id
 * @property string $last_read_message_at
 * @property string $last_read_at
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskConversationRead extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_conversation_read';
}
