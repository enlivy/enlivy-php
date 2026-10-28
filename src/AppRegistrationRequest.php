<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * An App drafted for its person to approve. `sealed_secrets` is set only on the answer to `claim()`,
 * once; every other answer carries none.
 *
 * @property string $id
 * @property string $object
 * @property string $status
 * @property string $user_id
 * @property string $organization_id
 * @property string|null $requested_by_oauth_client_id
 * @property array $manifest
 * @property string|null $manifest_version
 * @property array $documents
 * @property array $redirect_uris
 * @property array $permissions
 * @property array $declared_permissions
 * @property string $recipient_key
 * @property string $approval_url
 * @property string|null $user_app_id
 * @property string|null $oauth_client_id
 * @property string|null $organization_app_installation_id
 * @property string $expires_at
 * @property string|null $approved_at
 * @property string|null $claimed_at
 * @property string|null $cancelled_at
 * @property string $created_at
 * @property string $updated_at
 * @property array|null $sealed_secrets
 */
class AppRegistrationRequest extends ApiResource
{
    public const ?string OBJECT_NAME = 'app_registration_request';
}
