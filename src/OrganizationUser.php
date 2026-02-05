<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Organization User in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $user_id
 * @property string|null $organization_user_role_id
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone_number
 * @property string|null $phone_number_country_code
 * @property string|null $country_code
 * @property string|null $locale
 * @property string|null $timezone
 * @property string|null $organization_type
 * @property array $organization_information
 * @property bool $organization_is_eu_vat_registered
 * @property array $information
 * @property string|null $birthdate
 * @property string|null $bank_account_bank_name
 * @property string|null $bank_account_type
 * @property string|null $bank_account_currency
 * @property string|null $bank_account_country_code
 * @property string|null $bank_account_address
 * @property array $bank_account_information
 * @property string|null $payment_stripe_customer_id
 * @property string|null $last_used_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class OrganizationUser extends ApiResource
{
    public const ?string OBJECT_NAME = 'organization_user';
}
