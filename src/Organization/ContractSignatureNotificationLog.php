<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract Signature Notification Log in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_contract_id
 * @property string $organization_contract_signature_id
 * @property string|null $type
 * @property string|null $event
 * @property string|null $event_label
 * @property string|null $sent_to
 * @property string|null $sent_from
 * @property string|null $sent_from_name
 * @property string|null $subject
 * @property string|null $status
 * @property string|null $provider_message_id
 * @property string|null $verified_at
 * @property string|null $ip_address
 * @property string $created_at
 */
class ContractSignatureNotificationLog extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_signature_notification_log';
}
