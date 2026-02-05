<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Product in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_tax_class_id
 * @property string|null $alias
 * @property string $type
 * @property string|null $description
 * @property array $price_map
 * @property bool $price_is_tax_inclusive
 * @property string $primary_currency
 * @property array $name_lang_map
 * @property array $description_lang_map
 * @property array $unit_lang_map
 * @property array $invoice_schema_map
 * @property string|null $ean_number
 * @property string|null $upc_number
 * @property bool $is_sold
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Product extends ApiResource
{
    public const ?string OBJECT_NAME = 'product';
}
