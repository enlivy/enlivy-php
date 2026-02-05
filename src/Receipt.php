<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Receipt in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_receipt_prefix_id
 * @property string|null $organization_invoice_id
 * @property string $number
 * @property string $formatted_number
 * @property string $currency
 * @property float $total
 * @property string|null $issued_at
 * @property string $created_at
 * @property string $updated_at
 */
class Receipt extends ApiResource
{
    public const ?string OBJECT_NAME = 'receipt';
}
