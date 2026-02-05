<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a User Address in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_user_id
 * @property string|null $name
 * @property string|null $country_code
 * @property string|null $address_line_1
 * @property string|null $address_line_2
 * @property string|null $address_city
 * @property string|null $address_county
 * @property string|null $address_state
 * @property string|null $address_iso_3166
 * @property string|null $address_zip_code
 * @property string|null $address_hash
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class UserAddress extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_address';
}
