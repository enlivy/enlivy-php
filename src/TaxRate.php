<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Tax Rate in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_tax_class_id
 * @property string $name
 * @property float $rate
 * @property string $country_code
 * @property bool $is_compound
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class TaxRate extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_rate';
}
