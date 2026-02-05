<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an OAuth Token in the Enlivy API.
 *
 * @property string $id
 * @property string $user_id
 * @property string $client_id
 * @property string $name
 * @property array $scopes
 * @property bool $revoked
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $updated_at
 */
class OAuthToken extends ApiResource
{
    public const ?string OBJECT_NAME = 'oauth_token';
}
