<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_conversation_id
 * @property string|null $author_organization_user_id
 * @property string|null $author_organization_prospect_id
 * @property string|null $created_by_user_id
 * @property string $type
 * @property string $content_type
 * @property string|null $content
 * @property string|null $author_name
 * @property string|null $author_avatar_url
 * @property string|null $author_email
 * @property string|null $external_message_id
 * @property string|null $in_reply_to_external_message_id
 * @property array|null $metadata
 * @property string|null $redacted_at
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskConversationMessage extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_conversation_message';
}
