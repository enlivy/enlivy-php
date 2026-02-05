<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract Signature in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_contract_id
 * @property string|null $organization_contract_party_id
 * @property string $status
 * @property string|null $signature_source
 * @property string|null $signature_type
 * @property string|null $signature_image_file_disk
 * @property string|null $signature_image_file_path
 * @property string|null $signature_image_path
 * @property array|null $sign_session_required_confirmations
 * @property array|null $sign_session_signature_types
 * @property array|null $sign_session_confirmations_at
 * @property string|null $sign_session_ip_address
 * @property string|null $sign_session_user_agent
 * @property bool $is_signed
 * @property string|null $signed_at
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ContractSignature extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_signature';
}
