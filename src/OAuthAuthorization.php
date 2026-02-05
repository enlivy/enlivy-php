<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an OAuth Authorization in the Enlivy API.
 *
 * @property string $id
 * @property string $user_id
 * @property string $client_id
 * @property array $scopes
 * @property bool $revoked
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $updated_at
 */
class OAuthAuthorization extends ApiResource
{
    public const ?string OBJECT_NAME = 'oauth_authorization';
}
