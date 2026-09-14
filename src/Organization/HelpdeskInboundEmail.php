<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_helpdesk_inbox_id
 * @property string|null $organization_api_credential_id
 * @property string|null $organization_helpdesk_conversation_id
 * @property string|null $organization_helpdesk_conversation_message_id
 * @property string $from_email_address
 * @property string|null $from_name
 * @property array|null $to_email_addresses
 * @property array|null $cc_email_addresses
 * @property string|null $subject
 * @property string|null $content
 * @property string|null $header_message_id
 * @property string|null $header_in_reply_to
 * @property string|null $header_references
 * @property string|null $interpretation
 * @property string|null $error_message
 * @property string|null $bounced_email_address
 * @property string|null $processed_at
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskInboundEmail extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_inbound_email';
}
