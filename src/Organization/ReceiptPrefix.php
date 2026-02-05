<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Receipt Prefix in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $name
 * @property string|null $prefix
 * @property string|null $suffix
 * @property int $current_number
 * @property bool $has_year_prefix
 * @property bool $has_month_prefix
 * @property bool $has_day_prefix
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ReceiptPrefix extends ApiResource
{
    public const ?string OBJECT_NAME = 'receipt_prefix';
}
