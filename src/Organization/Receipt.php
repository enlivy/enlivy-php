<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Receipt in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_receipt_prefix_id
 * @property string|null $organization_invoice_id
 * @property string|null $organization_bank_account_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_contract_id
 * @property string|null $receipt_number
 * @property string $status
 * @property string $direction
 * @property string $currency
 * @property float $sub_total
 * @property float $discount
 * @property float $tax_total
 * @property float $total
 * @property bool $is_reverse
 * @property bool $is_tax_charged
 * @property string|null $file_extension
 * @property string|null $issued_at
 * @property string|null $due_at
 * @property string|null $paid_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Receipt extends ApiResource
{
    public const ?string OBJECT_NAME = 'receipt';
}
