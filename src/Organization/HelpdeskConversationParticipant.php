<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_conversation_id
 * @property string|null $organization_user_id
 * @property string|null $organization_prospect_id
 * @property string|null $name
 * @property string $email
 * @property bool $notifications_enabled
 * @property string $source
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskConversationParticipant extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_conversation_participant';
}
