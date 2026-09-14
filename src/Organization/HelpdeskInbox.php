<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $owner_organization_helpdesk_teammate_id
 * @property string|null $default_organization_helpdesk_teammate_id
 * @property string|null $organization_api_credential_id
 * @property string|null $branding_logo_organization_file_id
 * @property string|null $branding_icon_organization_file_id
 * @property string $name
 * @property string $number_prefix
 * @property int $conversation_current_number
 * @property string|null $display_name
 * @property string|null $branding_name
 * @property bool $is_personal
 * @property array|null $welcome_title_lang_map
 * @property array|null $welcome_description_lang_map
 * @property array|null $success_title_lang_map
 * @property array|null $success_description_lang_map
 * @property array|null $auto_response_chat_lang_map
 * @property array|null $auto_response_email_lang_map
 * @property array|null $out_of_office_message_lang_map
 * @property array|null $inactivity_reminder_message_lang_map
 * @property array|null $auto_resolved_message_lang_map
 * @property array|null $sender_name_lang_map
 * @property string|null $sender_email_address
 * @property array|null $widget_origins
 * @property string $locale
 * @property array|null $locale_list
 * @property string|null $timezone
 * @property array|null $working_hours
 * @property string $assignment_mode
 * @property string|null $expected_reply_time
 * @property string $email_collect_mode
 * @property string $email_style
 * @property bool $single_open_conversation_enabled
 * @property bool $is_visitor_tracking_enabled
 * @property array|null $tracked_query_parameters
 * @property bool $auto_response_enabled
 * @property bool $allow_messages_after_resolved
 * @property int|null $close_after_resolved_days
 * @property int|null $inactivity_reminder_days
 * @property int|null $auto_resolve_after_days
 * @property bool $is_active
 * @property bool $is_default
 * @property int $order
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class HelpdeskInbox extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_inbox';
}
