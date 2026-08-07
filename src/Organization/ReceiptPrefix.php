<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $name
 * @property string|null $description
 * @property int $current_number
 * @property string|null $prefix
 * @property string|null $suffix
 * @property bool $has_year_prefix
 * @property bool $has_month_prefix
 * @property bool $has_day_prefix
 * @property bool $reset_yearly
 * @property int|null $counter_year
 * @property string $formatted_number
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class ReceiptPrefix extends ApiResource
{
    public const ?string OBJECT_NAME = 'receipt_prefix';
}
