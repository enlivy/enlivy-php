<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a User in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string|null $user_account_type_id
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $phone_number
 * @property string|null $phone_number_country_code
 * @property string|null $phone_verified_at
 * @property string|null $invitation_code
 * @property string $locale
 * @property string $country_code
 * @property string $timezone
 * @property string|null $email_verification_sent_at
 * @property string|null $phone_verification_sent_at
 * @property string|null $remember_token
 * @property string|null $last_login_at
 * @property string $created_at
 * @property string $updated_at
 * @property array|null $capabilities
 */
class User extends ApiResource
{
    public const ?string OBJECT_NAME = 'user';
}
