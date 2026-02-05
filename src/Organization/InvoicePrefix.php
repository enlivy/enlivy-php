<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Invoice Prefix in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $prefix
 * @property int $next_number
 * @property string $format
 * @property bool $is_default
 * @property string $created_at
 * @property string $updated_at
 */
class InvoicePrefix extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice_prefix';
}
