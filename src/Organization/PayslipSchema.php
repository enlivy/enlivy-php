<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Payslip Schema in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property array $fields
 * @property bool $is_default
 * @property string $created_at
 * @property string $updated_at
 */
class PayslipSchema extends ApiResource
{
    public const ?string OBJECT_NAME = 'payslip_schema';
}
