<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $branding_name
 * @property string|null $branding_logo_organization_file_id
 * @property string|null $branding_icon_organization_file_id
 * @property array|null $welcome_title_lang_map
 * @property array|null $welcome_description_lang_map
 * @property array|null $success_title_lang_map
 * @property array|null $success_description_lang_map
 * @property array|null $out_of_office_message_lang_map
 * @property array|null $sender_name_lang_map
 * @property int $close_after_resolved_days
 * @property int|null $inactivity_reminder_days
 * @property int|null $auto_resolve_after_days
 * @property int|null $visitor_event_retention_days
 * @property int|null $visitor_retention_days
 * @property array|null $inactivity_reminder_message_lang_map
 * @property array|null $auto_resolved_message_lang_map
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskSettings extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_settings';
}
