<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_payslip_schema_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_contract_id
 * @property string|null $organization_employment_id
 * @property string|null $period_start
 * @property string|null $period_end
 * @property string $status
 * @property string|null $payment_method
 * @property float $gross_total
 * @property float $employee_contributions_total
 * @property float $tax_relief_total
 * @property float $taxable_amount
 * @property float $net_total
 * @property float $tax_total
 * @property float $post_tax_deductions_total
 * @property float $paid_total
 * @property float $employer_contributions_total
 * @property float $total
 * @property string $currency
 * @property string|null $issued_at
 * @property string|null $paid_at
 * @property array|null $information
 * @property string|null $deleted_by_user_id
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class Payslip extends ApiResource
{
    public const ?string OBJECT_NAME = 'payslip';
}
