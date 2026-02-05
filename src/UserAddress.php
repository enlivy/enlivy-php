<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a User Address in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_user_id
 * @property string $type
 * @property string|null $label
 * @property string $address_line_1
 * @property string|null $address_line_2
 * @property string $address_city
 * @property string|null $address_county
 * @property string|null $address_state
 * @property string|null $address_iso_3166
 * @property string $address_zip_code
 * @property string $country_code
 * @property bool $is_default
 * @property string $created_at
 * @property string $updated_at
 */
class UserAddress extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_address';
}
