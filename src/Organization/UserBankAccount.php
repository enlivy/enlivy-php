<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Organization User Bank Account in the Enlivy API.
 *
 * A bank account belonging to an organization user (customer/employee), stored in its
 * own table separate from the organization's own bank accounts.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_user_id
 * @property string $type
 * @property string|null $bank_name
 * @property string|null $currency
 * @property string|null $country_code
 * @property string|null $address
 * @property array $information
 * @property bool $is_primary
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class UserBankAccount extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_bank_account';
}
