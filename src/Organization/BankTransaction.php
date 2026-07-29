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
 * @property string|null $hash
 * @property string|null $title
 * @property float $amount
 * @property string $currency
 * @property string $direction
 * @property string|null $note
 * @property string $organization_bank_account_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $sender_label
 * @property string|null $receiver_label
 * @property string|null $organization_bank_transaction_cost_type_id
 * @property string|null $state
 * @property string|null $stripe_payout_id
 * @property string|null $stripe_reporting_category
 * @property array|null $currency_conversion_information
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class BankTransaction extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_transaction';
}
