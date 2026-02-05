<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an OAuth Client in the Enlivy API.
 *
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $secret
 * @property string $redirect
 * @property bool $personal_access_client
 * @property bool $password_client
 * @property bool $revoked
 * @property string $created_at
 * @property string $updated_at
 */
class OAuthClient extends ApiResource
{
    public const ?string OBJECT_NAME = 'oauth_client';
}
