<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $organization_billing_package_id
 * @property string|null $organization_billing_package_payment_plan_id
 * @property string|null $organization_billing_package_subscription_term_id
 * @property string|null $access_token
 * @property string $status
 * @property string|null $organization_prospect_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $recipient_email
 * @property string|null $recipient_name
 * @property string|null $organization_sender_user_id
 * @property string $currency
 * @property string|null $billed_currency
 * @property float $sub_total
 * @property float $discount
 * @property float $total
 * @property array|null $allowed_payment_methods
 * @property array|null $organization_bank_account_ids
 * @property string|null $outcome_mode
 * @property string|null $currency_conversion_fee
 * @property bool $is_expired
 * @property bool $has_unsigned_required_contracts
 * @property bool $can_create_billing_schedule
 * @property string|null $sent_at
 * @property string|null $expires_at
 * @property string|null $expired_at
 * @property string|null $expired_by_user_id
 * @property string|null $viewed_at
 * @property string|null $accepted_at
 * @property string|null $rejected_at
 * @property string|null $organization_billing_schedule_id
 * @property string|null $organization_invoice_id
 * @property string|null $organization_proforma_invoice_id
 * @property array|null $note_lang_map
 * @property string|null $accepted_by_ip
 * @property string|null $accepted_by_user_agent
 * @property string|null $rejected_by_ip
 * @property string|null $rejected_by_user_agent
 * @property string|null $rejected_reason
 * @property string|null $created_by_user_id
 * @property string|null $deleted_by_user_id
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class Proposal extends ApiResource
{
    public const ?string OBJECT_NAME = 'proposal';
}
