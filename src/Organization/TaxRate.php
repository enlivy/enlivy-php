<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_tax_class_id
 * @property string|null $seller_country_code
 * @property string|null $name
 * @property array|null $name_lang_map
 * @property string|null $display_name
 * @property array|null $display_name_lang_map
 * @property string|null $retired_at
 * @property array|null $retired_reason_lang_map
 * @property string|null $retired_by_user_id
 * @property float $rate
 * @property bool $has_locations
 * @property bool|null $is_business_entity
 * @property bool|null $is_eu_vat_registered
 * @property bool $has_eu_vat_properties
 * @property string|null $eu_vat_class
 * @property bool $is_vat_exempt
 * @property string|null $vatex_code
 * @property bool $is_compound
 * @property bool $is_inclusive
 * @property int|null $priority
 * @property string|null $stripe_tax_rate_id
 * @property string|null $auto_imported_from
 * @property string|null $auto_imported_hash
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxRate extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_rate';
}
