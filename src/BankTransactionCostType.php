<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Bank Transaction Cost Type in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_expense
 * @property string $created_at
 * @property string $updated_at
 */
class BankTransactionCostType extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_transaction_cost_type';
}
