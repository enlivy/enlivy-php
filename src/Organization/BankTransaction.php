<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Bank Transaction in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_bank_account_id
 * @property string|null $organization_bank_transaction_cost_type_id
 * @property string $type
 * @property float $amount
 * @property string $currency
 * @property string|null $description
 * @property string|null $reference
 * @property string $transaction_date
 * @property string $created_at
 * @property string $updated_at
 */
class BankTransaction extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_transaction';
}
