<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $organization_receiver_user_id
 * @property string $organization_sender_user_id
 * @property string $organization_billing_package_id
 * @property string|null $organization_billing_package_subscription_term_id
 * @property string|null $organization_billing_package_payment_plan_id
 * @property array|null $selected_group_items
 * @property array|null $line_quantities
 * @property string $currency
 * @property string|null $start_at
 * @property string $mode
 * @property string $status
 * @property string $payment_status
 * @property string|null $payment_method_kind
 * @property array|null $priced_lines
 * @property string|null $priced_sub_total
 * @property string|null $priced_tax_total
 * @property string|null $priced_total
 * @property array|null $priced_terms
 * @property string|null $priced_at
 * @property string|null $price_held_until
 * @property array|null $renewal
 * @property string|null $payment_provider_reference
 * @property string|null $organization_billing_schedule_id
 * @property string|null $organization_invoice_id
 * @property string|null $organization_proforma_invoice_id
 * @property string|null $client_reference_id
 * @property string|null $source_channel
 * @property string|null $source_medium
 * @property string|null $source_campaign
 * @property string|null $source_term
 * @property string|null $source_content
 * @property string|null $source_click_id
 * @property array<string, string|null>|null $metadata
 * @property string $expires_at
 * @property string|null $completed_at
 * @property string|null $expired_at
 * @property string $created_at
 * @property string $updated_at
 */
class CheckoutSession extends ApiResource
{
    public const ?string OBJECT_NAME = 'checkout_session';
}
