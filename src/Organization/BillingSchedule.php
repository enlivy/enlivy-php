<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Billing Schedule in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_contract_id
 * @property string|null $organization_bank_account_id
 * @property array|null $name_lang_map
 * @property array|null $note_lang_map
 * @property string $direction
 * @property string $type
 * @property string $status
 * @property string|null $payment_method
 * @property string $currency
 * @property float|null $total
 * @property float $paid_total
 * @property string $formatted_total
 * @property string $frequency
 * @property string|null $next_payment_create_at
 * @property string|null $last_payment_created_at
 * @property string|null $starts_at
 * @property string|null $ends_at
 * @property string|null $payment_stripe_account_id
 * @property string|null $payment_stripe_subscription_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class BillingSchedule extends ApiResource
{
    public const ?string OBJECT_NAME = 'billing_schedule';
}
