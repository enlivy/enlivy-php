<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string|null $description
 * @property string $token Masked - only the last 5 characters are returned
 * @property array $abilities
 * @property array $organizations Organization ids this token is scoped to; empty when unscoped
 * @property array|null $custom_data
 * @property string|null $user_agent
 * @property string|null $last_used_at
 * @property string $created_at
 * @property string $updated_at
 */
class UserToken extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_token';
}
