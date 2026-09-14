<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_conversation_id
 * @property string|null $organization_helpdesk_conversation_message_id
 * @property string|null $organization_helpdesk_visitor_id
 * @property string|null $uploaded_by_user_id
 * @property string $file_name
 * @property string $file_extension
 * @property string $file_mime_type
 * @property int $file_size
 * @property bool $is_staged
 * @property string|null $scanned_at
 * @property string|null $quarantined_at
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskConversationAttachment extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_conversation_attachment';
}
