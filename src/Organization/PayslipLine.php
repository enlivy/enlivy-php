<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_payslip_id
 * @property string|null $type
 * @property string|null $code
 * @property array|null $label_lang_map
 * @property float|null $quantity
 * @property string|null $unit_code
 * @property float|null $rate
 * @property float|null $amount
 * @property string|null $currency
 * @property string|null $source
 * @property string|null $source_reference_id
 * @property int $order
 * @property string $created_at
 * @property string $updated_at
 */
class PayslipLine extends ApiResource
{
    public const ?string OBJECT_NAME = 'payslip_line';
}
