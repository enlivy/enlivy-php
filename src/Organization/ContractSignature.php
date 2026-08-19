<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_contract_id
 * @property string|null $sign_session_token
 * @property array|null $sign_session_required_confirmations
 * @property array|null $sign_session_signature_types
 * @property array|null $sign_session_confirmations_at
 * @property string|null $signature_type
 * @property string|null $signature_source
 * @property bool $is_signed
 * @property string|null $signed_document_hash
 * @property bool $has_signature_image
 * @property bool $has_signature_events_log
 * @property bool $has_evidence_authentication
 * @property bool $has_evidence_consent
 * @property bool $has_evidence_signature_biometrics
 * @property string $status
 * @property string|null $expires_at
 * @property string|null $organization_contract_party_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ContractSignature extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_signature';
}
