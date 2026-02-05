<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tax Rate in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_tax_class_id
 * @property string|null $name
 * @property float $rate
 * @property string|null $country_code
 * @property bool $is_compound
 * @property bool $is_shipping
 * @property bool $is_inclusive
 * @property bool $has_locations
 * @property bool $has_eu_vat_properties
 * @property bool|null $is_business_entity
 * @property bool|null $is_eu_vat_registered
 * @property string|null $eu_vat_class
 * @property bool $is_vat_exempt
 * @property string|null $vatex_code
 * @property int|null $priority
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxRate extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_rate';
}
