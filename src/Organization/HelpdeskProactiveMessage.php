<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_inbox_id
 * @property string|null $sender_organization_helpdesk_teammate_id
 * @property string $name
 * @property array $message_lang_map
 * @property array|null $conditions
 * @property int $delay_seconds
 * @property int $cooldown_hours
 * @property bool $is_active
 * @property bool $is_online_required
 * @property bool $is_identity_required
 * @property bool $is_business_hours_only
 * @property int $order
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class HelpdeskProactiveMessage extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_proactive_message';
}
