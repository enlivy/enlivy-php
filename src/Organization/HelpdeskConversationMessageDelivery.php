<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_conversation_message_id
 * @property string|null $recipient_organization_user_id
 * @property string|null $recipient_organization_prospect_id
 * @property string $recipient_email_address
 * @property string|null $recipient_name
 * @property string|null $provider_message_id
 * @property string|null $sent_at
 * @property string|null $delivered_at
 * @property string|null $bounced_at
 * @property string|null $error_message
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskConversationMessageDelivery extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_conversation_message_delivery';
}
