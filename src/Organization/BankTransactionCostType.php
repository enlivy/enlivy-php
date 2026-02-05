<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Bank Transaction Cost Type in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_bank_transaction_cost_type_id
 * @property array|null $title_lang_map
 * @property bool $connection_required
 * @property array|null $connection_types
 * @property string|null $rgba_color_code
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class BankTransactionCostType extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_transaction_cost_type';
}
