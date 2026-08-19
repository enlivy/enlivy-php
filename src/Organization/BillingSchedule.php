<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_billing_package_id
 * @property string|null $organization_billing_package_subscription_term_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_contract_id
 * @property string|null $organization_bank_account_id
 * @property array|null $name_lang_map
 * @property array|null $note_lang_map
 * @property string $direction
 * @property string $status
 * @property string|null $payment_method
 * @property string $invoice_issue_trigger
 * @property string $currency
 * @property float|null $total
 * @property float|null $paid_total
 * @property string|null $next_payment_create_at
 * @property string|null $last_payment_created_at
 * @property string|null $starts_at
 * @property string|null $ends_at
 * @property string|null $cancel_effective_at
 * @property string|null $organization_user_payment_method_id
 * @property string|null $payment_provider_billing_reference
 * @property string|null $management_type
 * @property bool $is_email_notifications_active
 * @property bool $customer_can_reconfigure
 * @property bool $customer_can_cancel
 * @property bool $customer_can_pause
 * @property string|null $email_notifications_to
 * @property array|null $email_notifications_cc
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class BillingSchedule extends ApiResource
{
    public const ?string OBJECT_NAME = 'billing_schedule';
}
