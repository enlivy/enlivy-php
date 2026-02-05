<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Bank Account in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $bank_name
 * @property string $type
 * @property string $currency
 * @property string $country_code
 * @property string|null $iban
 * @property string|null $bic
 * @property string|null $account_number
 * @property string|null $routing_number
 * @property bool $is_default
 * @property string $created_at
 * @property string $updated_at
 */
class BankAccount extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_account';
}
