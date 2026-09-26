<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $organization_helpdesk_inbox_id
 * @property string|null $organization_project_id
 * @property string|null $organization_helpdesk_visitor_id
 * @property string|null $assigned_organization_helpdesk_teammate_id
 * @property string|null $contact_organization_user_id
 * @property string|null $contact_organization_prospect_id
 * @property string|null $merged_into_organization_helpdesk_conversation_id
 * @property string|null $continued_from_organization_helpdesk_conversation_id
 * @property int|null $number
 * @property string $display_number
 * @property string $source
 * @property string $state
 * @property string|null $contact_name
 * @property string|null $contact_email
 * @property string|null $locale
 * @property string|null $subject
 * @property string $priority
 * @property int|null $rating
 * @property string|null $rating_comment
 * @property string|null $rating_requested_at
 * @property string|null $rated_at
 * @property string|null $snoozed_until
 * @property string|null $resolved_at
 * @property string|null $closed_at
 * @property string|null $spam_at
 * @property int|null $unread_count
 * @property string|null $last_message_type
 * @property string|null $last_message_preview
 * @property string|null $last_message_at
 * @property array|null $lifecycle
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class HelpdeskConversation extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_conversation';
}
