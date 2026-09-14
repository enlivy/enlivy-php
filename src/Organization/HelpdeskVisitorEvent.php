<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $organization_helpdesk_visitor_id
 * @property string|null $organization_helpdesk_proactive_message_id
 * @property string|null $organization_helpdesk_conversation_id
 * @property string $event_type
 * @property string $occurred_at
 * @property string|null $url
 * @property string|null $title
 * @property array|null $metadata
 * @property string|null $created_at
 */
class HelpdeskVisitorEvent extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_visitor_event';
}
