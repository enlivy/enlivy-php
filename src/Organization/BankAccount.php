<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $name
 * @property float|null $balance
 * @property string $currency
 * @property string|null $bank_name
 * @property string|null $bank_address
 * @property string|null $bank_country_code
 * @property array|null $account_information
 * @property string $type
 * @property string|null $sync_provider
 * @property string|null $organization_bank_account_data_bridge_id
 * @property string|null $organization_bank_account_data_bridge_last_synced_at
 * @property string|null $organization_bank_account_data_bridge_account
 * @property string|null $organization_bank_account_data_bridge_sync_from
 * @property string|null $organization_bank_account_data_bridge_next_sync_at
 * @property array|null $payment_qr_types
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class BankAccount extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_account';
}
