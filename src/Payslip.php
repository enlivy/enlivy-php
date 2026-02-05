<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Payslip in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_user_id
 * @property string|null $organization_payslip_schema_id
 * @property string $period_start
 * @property string $period_end
 * @property float $gross_amount
 * @property float $net_amount
 * @property string $currency
 * @property array $data
 * @property string|null $issued_at
 * @property string $created_at
 * @property string $updated_at
 */
class Payslip extends ApiResource
{
    public const ?string OBJECT_NAME = 'payslip';
}
