<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Invoice in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_invoice_id
 * @property string|null $organization_invoice_prefix_id
 * @property string|null $organization_bank_account_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_receiver_user_address_id
 * @property string|null $organization_receiver_shipping_user_address_id
 * @property string|null $organization_contract_id
 * @property string|null $api_charged_organization_id
 * @property string|null $number
 * @property string $formatted_number
 * @property string $formatted_number_plain
 * @property string $formatted_total
 * @property string $status
 * @property string $status_color
 * @property string $type
 * @property string $direction
 * @property string $source
 * @property string|null $payment_method
 * @property string|null $delivery_method
 * @property array|null $delivery_method_information
 * @property string $currency
 * @property float $sub_total
 * @property float $tax_total
 * @property float $discount
 * @property float $total
 * @property bool $is_tax_charged
 * @property bool $is_api_charge
 * @property bool $is_downloadable
 * @property bool $receiver_has_custom_identity
 * @property bool|null $receiver_custom_identity_is_business
 * @property string|null $receiver_custom_identity_organization_type
 * @property string|null $receiver_custom_identity_name
 * @property bool $sender_has_custom_identity
 * @property bool|null $sender_custom_identity_is_business
 * @property string|null $sender_custom_identity_organization_type
 * @property string|null $sender_custom_identity_name
 * @property array|null $receiver_information
 * @property array|null $receiver_organization_information
 * @property string|null $receiver_country_code
 * @property array|null $sender_information
 * @property array|null $sender_organization_information
 * @property string|null $sender_country_code
 * @property array|null $note_lang_map
 * @property string|null $peppol_project_reference
 * @property string|null $peppol_purchase_order_reference
 * @property string|null $peppol_sales_order_reference
 * @property array|null $peppol_exchange_push_options
 * @property string|null $peppol_exchange_scheduled_push_at
 * @property string|null $peppol_exchange_scheduled_pushed_at
 * @property array|null $peppol_exchanges_pushed
 * @property string|null $payment_stripe_account_id
 * @property string|null $payment_stripe_charge_id
 * @property string|null $payment_stripe_customer_id
 * @property string|null $payment_stripe_intent_id
 * @property string|null $payment_stripe_payment_method_id
 * @property string|null $payment_stripe_setup_id
 * @property string|null $payment_stripe_subscription_id
 * @property string|null $file_extension
 * @property array|null $bank_account_payment_qr_types
 * @property string|null $issued_at
 * @property string|null $due_at
 * @property string|null $paid_at
 * @property string|null $finalized_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Invoice extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice';
}
