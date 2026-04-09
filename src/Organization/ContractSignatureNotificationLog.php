<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract Signature Notification Log in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_contract_id
 * @property string $organization_contract_signature_id
 * @property string|null $type
 * @property string|null $status
 * @property string $created_at
 * @property string $updated_at
 */
class ContractSignatureNotificationLog extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_signature_notification_log';
}
