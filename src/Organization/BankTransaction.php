<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Bank Transaction in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_bank_account_id
 * @property string|null $organization_bank_transaction_cost_type_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string $direction
 * @property string $source
 * @property float $amount
 * @property string $currency
 * @property string|null $source_currency
 * @property array|null $currency_conversion_information
 * @property string|null $description
 * @property string|null $reference
 * @property string|null $sender_name
 * @property string|null $sender_iban
 * @property string|null $receiver_name
 * @property string|null $receiver_iban
 * @property string|null $external_id
 * @property string|null $transaction_date
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class BankTransaction extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_transaction';
}
