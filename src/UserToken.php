<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a User Token in the Enlivy API.
 *
 * @property string $id
 * @property string $name
 * @property string $token
 * @property array $abilities
 * @property string|null $last_used_at
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $updated_at
 */
class UserToken extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_token';
}
