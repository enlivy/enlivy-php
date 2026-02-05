<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Bank Account in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_bank_account_data_bridge_id
 * @property string $name
 * @property string $type
 * @property string|null $bank_name
 * @property string|null $bank_country_code
 * @property string $currency
 * @property array|null $account_information
 * @property float|null $balance
 * @property array|null $payment_qr_types
 * @property string|null $organization_bank_account_data_bridge_external_id
 * @property string|null $organization_bank_account_data_bridge_last_synced_at
 * @property string|null $organization_bank_account_data_bridge_sync_from
 * @property string|null $organization_bank_account_data_bridge_next_sync_at
 * @property string|null $organization_bank_account_data_bridge_amount_synced_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class BankAccount extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_account';
}
