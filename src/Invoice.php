<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Invoice in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_invoice_prefix_id
 * @property string|null $organization_bank_account_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_contract_id
 * @property string $number
 * @property string $formatted_number
 * @property string $status
 * @property string $type
 * @property string $direction
 * @property string $source
 * @property string $payment_method
 * @property string $delivery_method
 * @property array $delivery_method_information
 * @property string $currency
 * @property float $sub_total
 * @property float $tax_total
 * @property float $discount
 * @property float $total
 * @property bool $is_tax_charged
 * @property bool $is_downloadable
 * @property array $note_lang_map
 * @property string|null $issued_at
 * @property string|null $due_at
 * @property string|null $paid_at
 * @property string|null $finalized_at
 * @property array $receiver_information
 * @property array $sender_information
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Invoice extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice';
}
