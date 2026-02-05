<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Invoice Prefix in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $custom_logo_file_id
 * @property string $name
 * @property string $alias
 * @property string|null $description
 * @property float $current_number
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class InvoicePrefix extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice_prefix';
}
