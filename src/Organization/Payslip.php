<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Payslip in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_payslip_schema_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_contract_id
 * @property string $status
 * @property string|null $payment_method
 * @property string $currency
 * @property float $net_total
 * @property float $tax_total
 * @property float $total
 * @property array|null $information
 * @property string|null $issued_at
 * @property string|null $paid_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Payslip extends ApiResource
{
    public const ?string OBJECT_NAME = 'payslip';
}
