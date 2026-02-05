<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a User in the Enlivy API.
 *
 * @property string $id
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string $phone_number
 * @property string $locale
 * @property string $country_code
 * @property string $timezone
 * @property string|null $email_verified_at
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ApiResource
{
    public const ?string OBJECT_NAME = 'user';
}
