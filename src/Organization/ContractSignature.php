<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract Signature in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_contract_id
 * @property string $organization_user_id
 * @property string $type
 * @property string|null $signature_data
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $signed_at
 * @property string $created_at
 * @property string $updated_at
 */
class ContractSignature extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_signature';
}
