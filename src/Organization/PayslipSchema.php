<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Payslip Schema in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property array|null $name_lang_map
 * @property array|null $description_lang_map
 * @property array|null $field_map
 * @property array|null $field_group_list
 * @property string|null $information_key_net_total
 * @property string|null $information_key_tax_total
 * @property string|null $information_key_total
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class PayslipSchema extends ApiResource
{
    public const ?string OBJECT_NAME = 'payslip_schema';
}
